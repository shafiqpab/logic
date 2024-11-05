<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
extract($_REQUEST);

function toRound($number)
{
    
    
    return number_format($number, 2,'.','');
}

$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');

$subcon_buyer_arr=return_library_array( "select id,cust_buyer from subcon_ord_dtls where status_active=1 and is_deleted=0 order by cust_buyer",'id','cust_buyer');

//$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
//$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
$company_arr = return_library_array("select id, company_name from lib_company order by company_name","id","company_name");
if($action=="show_dtls_listview")
{
	

    ?>
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="150" align="center">Rate Category/For</th>
                <th width="110" align="center">Order</th>
                <th width="80" align="center">Style</th>
                <th width="80" align="center">Job</th>
                <th width="110" align="center">Process</th>
                <th width="110" align="center">Uom</th>
                <th width="120" align="center">Rate</th>
                <th width="120" align="center">Currency</th>
                <th align="center">Status</th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
            <?php
                $i=1;
                $sql ="SELECT c.id,c.rate_category,c.process_id,c.uom,c.currency,c.rate,c.status,c.po_ids,d.job_no as job_no_mst,d.style_ref_no from process_order_wise_rate_entry_mst a,process_order_wise_rate_entry_dtls c, wo_po_details_master d where a.id=c.mst_id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$data" ;
                // echo $sql; die;
                $po_ids='';
                foreach(sql_select($sql) as $v)
                {
                    $po_ids .= ($po_ids=="") ? $v['PO_IDS'] : ",".$v['PO_IDS'];
                }
                $order_arr=return_library_array( "SELECT id, po_number from WO_PO_BREAK_DOWN where id in($po_ids)",'id','po_number');

                foreach(sql_select($sql) as $val)
                {
                    $po_arr = explode(",",$val['PO_IDS']);
                    $po_name = '';
                    foreach ($po_arr as $r) 
                    {
                        $po_name .= ($po_name=="") ? $order_arr[$r] : ",".$order_arr[$r];
                    }
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="fnc_load_from_dtls(<? echo $val[csf('id')]; ?>);">
                        <td width="40" align="center"><? echo $i; ?></td>
                        <td width="150" align="center"><? echo $rate_category_array[$val['RATE_CATEGORY']]; ?></td>
                        <td width="110" align="center"><p><?=$po_name;?></p></td>
                        <td width="80" align="center"><? echo $val['STYLE_REF_NO']?></td>
                        <td width="80" align="center"><? echo $val['JOB_NO_MST']?></td>
                        <td width="110" align="center"><? echo $process_array[$val['PROCESS_ID']];?></td>
                        <td width="110" align="center"><? echo $unit_of_measurement[$val['UOM']];?></td>
                        <td width="120" align="center"><? echo $val['RATE'];?></td>
                        <td width="120" align="center"><? echo $currency[$val['CURRENCY']];?></td>
                        <td align="center"><? echo $row_status[$val['STATUS']];?></td>

                            
                    
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

if($action=="populate_input_form_data")
{
    $sqlResult =sql_select("SELECT c.id,c.company_id,c.rate_category,c.po_ids,c.process_id,c.uom,c.currency,c.rate,c.status,d.job_no as job_no_mst,c.job_id,c.entry_date from process_order_wise_rate_entry_mst a,process_order_wise_rate_entry_dtls c, wo_po_details_master d  where a.id=c.mst_id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.id=$data");
    //echo $sqlResult;die;
    $po_ids_array=array();
    foreach($sqlResult as $val) 
    {
        $po_ids_array[$val['PO_IDS']] =$val['PO_IDS'];
    }
    //echo "<pre>";print_r($po_ids_array);die;
    $po_ids=implode(",",$po_ids_array);
    $po_sql=sql_select("SELECT id,po_number from wo_po_break_down where id in($po_ids)  and status_active=1 and is_deleted=0 ");
    // echo $po_sql;die;
    $po_no_arr=array();
    foreach($po_sql as $val) 
    {
        $po_no_arr[$val['ID']] =$val['PO_NUMBER'];
    }
    //echo "<pre>";print_r($po_no_arr);die;
    foreach($sqlResult as $result)
	{
        $po_arr = explode(",",$result['PO_IDS']);
        $po_name = '';
        foreach ($po_arr as $r) 
        {
            $po_name .= ($po_name=="") ? $po_no_arr[$r] : ",".$po_no_arr[$r];
        }
		echo "$('#txt_date').val('".change_date_format($result[csf('entry_date')])."');\n";	
        echo "$('#cbo_company_id').val('".$result[csf('company_id')]."');\n";
        echo "$('#cbo_rate_category').val('".$result[csf('rate_category')]."');\n";
        echo "$('#txt_style_ref').val('".$po_name."');\n";
        echo "$('#cbo_process').val('".$result[csf('process_id')]."');\n";
        echo "$('#cbo_uom').val('".$result[csf('uom')]."');\n";
        echo "$('#cbo_currency').val('".$result[csf('currency')]."');\n";
        echo "$('#txt_exchange_rate').val('".$result[csf('rate')]."');\n";
        echo "$('#cbo_status').val('".$result[csf('status')]."');\n";
        echo "$('#hidden_job_id').val('".$result[csf('job_id')]."');\n";
        echo "$('#hidden_po_id').val('".$result[csf('po_ids')]."');\n";
        echo "$('#dtls_id').val('".$result[csf('id')]."');\n";
        echo "set_button_status(1, permission, 'fnc_process_rate',1);\n";

    }    
}


if ($action=="systemId_popup")
{
    echo load_html_head_contents("System ID Pop-UP", "../../", 1, 1,'','','');
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
    <div align="center" style="width:840px;">
        <form name="searchsystemidfrm"  id="searchsystemidfrm">
            <fieldset style="width:830px;">
           
                <table cellpadding="0" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th>Company Name</th>
                        <th>Year</th>
                        <th>Job No</th>
                        <!-- <th>Order No</th> -->
                        <th>Style Ref</th>
                        
                        <th colspan="2">Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                            
                            <input type="hidden" id="hidden_mst_id">
                        </th>
                    </thead>
                    <tr>
                   <td>
										<?
											echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--", 0, " "); 
										
										?>
									</td>
                                    <td align="center">
                                        <?
                                            $year_current=date("Y");
                                            echo create_drop_down( "cbo_job_year", 50, $year,"", 1, "All",$year_current,'','');
                                        ?>
                                    </td>
                        </td>
                        <td align="center">
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px;"/>
                        </td>
                        <!-- <td align="center">
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:100px;"/>
                        </td> -->
                        <td align="center">
                            <input type="text" id="txt_style_ref" name="txt_style_ref" class="text_boxes" style="width:100px;"/>
                        </td>
                        
                        <td align="center">
                            <input type="text" style="width:100px;" class="datepicker"  name="txt_date_from" id="txt_date_from" readonly />   
                        </td>
                        <td align="center">
                            <input type="text" style="width:100px;" class="datepicker"  name="txt_date_to" id="txt_date_to" readonly />   
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'price_rate_list_view', 'search_div', 'process_order_wise_rate_entry_for_pcs_rate_worker_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8"><?=load_month_buttons(1);?></td>
                    </tr>
                </table>
                <table width="100%" style="margin-top:5px;">
                    <tr>
                        <td colspan="5">
                            <div style="margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
    </body>
    <script>
        document.getElementById('cbo_company_id').value='<?=$cbo_company_id;?>';
    </script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="price_rate_list_view")
{
    list($company_id,$year,$job_no,$style_ref,$from_date,$to_date)=explode("_",$data);  
  

    
    if($sysid=='')$sysid=" "; else $sysid=" and a.system_no like('%".trim($sysid)."%')";  
    if($job_no=='')$job_no_cond=" "; else $job_no_cond=" and d.job_no like('%".trim($job_no)."')";  
    // if($order_no=='')$order_no_cond=" "; else $order_no_cond=" and b.po_number like '%$order_no%'"; 
    if($style_ref=='')$style_ref_cond=" "; else $style_ref_cond=" and d.style_ref_no like '%$style_ref%'";  
    if($company_id=='')$company_id_cond=" "; else $company_id_cond=" and a.company_id=$company_id";    
  
   

    //if($wo_no=="")$order_con=" "; else $order_con=" and c.sys_number like('%".$wo_no."%') ";    

    if($from_date!='' && $to_date!=''){ 
        if($db_type==0){
            
            $from_date=change_date_format($from_date);
            $to_date=change_date_format($to_date);
        }
        else
        {
            $from_date=change_date_format($from_date,'','',-1);
            $to_date=change_date_format($to_date,'','',-1);
        }
        $date_con=" and c.entry_date BETWEEN '$from_date' and '$to_date'";   
    }
    else
    {
        $date_con="";   
    }
    if ($year==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year";
    
        $sql =("SELECT a.id,a.system_no,c.company_id,c.rate_category,c.process_id,c.po_ids,d.job_no as job_no_mst,c.entry_date,d.style_ref_no from process_order_wise_rate_entry_mst a,process_order_wise_rate_entry_dtls c,wo_po_details_master d where a.id=c.mst_id and c.job_id=d.id  $sysid $date_con $job_no_cond $order_no_cond $style_ref_cond $year_id_cond $company_id_cond and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0");
    //    echo $sql;die;
    
    
    $result = sql_select($sql);
    $po_ids='';
    foreach($result as $v)
    {
        $po_ids .= ($po_ids=="") ? $v['PO_IDS'] : ",".$v['PO_IDS'];
    }
    $order_arr=return_library_array( "SELECT id, po_number from WO_PO_BREAK_DOWN where id in($po_ids)",'id','po_number');

    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="150">System ID</th>
            <th width="150">Rate Category/ For</th>
            <th width="150">Order </th>
            <th width="150">Style</th>
            <th width="150">Job</th>
            <th width="150">Process</th>
        </thead>
    </table>
    <div style="width:865px;" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="847" class="rpt_table" id="tbl_list_search">  
        <? 
            $i=1;
            foreach ($result as $row)
            {  
                
                $po_arr = explode(",",$row['PO_IDS']);
                $po_name = '';
                foreach ($po_arr as $r) 
                {
                    $po_name .= ($po_name=="") ? $order_arr[$r] : ",".$order_arr[$r];
                }

                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
            
            ?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value(<? echo $row[csf('id')]; ?>)" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $row[csf('system_no')]; ?></p></td>
                    <td width="150" align="center"><p><? echo $rate_category_array[$row['RATE_CATEGORY']]; ?></p></td>
                    <td width="150" align="center"><p><? echo $po_name; ?></p></td>
                    <td width="150" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td width="150" align="center"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
                    <td width="150" align="center"><p><? echo $process_array[$row['PROCESS_ID']] ?></p></td>
                            
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

if($action=="style_popup")
{
	// echo load_html_head_contents("Style Info", "../../", 1, 1,'','','');
    // echo load_html_head_contents("Style Info","../../", 1, 1, "",'1','',1,'');
    echo load_html_head_contents("Style Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $company;die;
	?>
	
	<script>
        var selected_id = new Array; var selected_job_id = new Array;var selected_po = new Array;var selected_id_arr = new Array;var selected_job_id_arr = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function hasDuplicates(arr) 
        {
            return new Set(arr).size !== arr.length;
        }

       
		function js_set_value( str ) 
        {
            if (str!="") str=str.split("_");
            if($("#tr_"+str[0]).css("display") !='none')
            {  
                let selected_job_id_arr = document.getElementById('hide_job_id').value.split('*');
                if ( selected_job_id_arr[0]!="" && selected_job_id_arr[0]!=str[2] )
                {
                    alert('Job Mixing Not Allowed.');
                    return;
                }
                toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
                
                if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
                    selected_id_arr.push( str[0] );
                    selected_id.push( str[1] );
                    selected_job_id.push( str[2] );
                    selected_po.push( str[3] );
                    
                }
                else {
                    for( var i = 0; i < selected_id.length; i++ ) {
                        if( selected_id[i] == str[1] ) break;
                    }
                    selected_id.splice( i, 1 );
                    selected_job_id.splice( i, 1 );
                    selected_po.splice( i, 1 );
                }
                var id = ''; var job_id = '';var po_no = '';
                for( var i = 0; i < selected_id.length; i++ ) {
                    id += selected_id[i] + ',';
                    job_id += selected_job_id[i] + '*';
                    po_no += selected_po[i] + '*';
                }
                
                id = id.substr( 0, id.length - 1 );
                job_id = job_id.substr( 0, job_id.length - 1 );
                po_no = po_no.substr( 0, po_no.length - 1 );
                
                $('#hide_po_id').val( id );
                $('#hide_po_no').val( po_no );
                $('#hide_job_id').val( job_id );
            }
		}
   /*  function js_set_value(id)
    {
		//alert(id);
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
    } */
    </script>
    </head>
    <body>
    <div align="center" style="width:900px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:880px;">
            <table width="880" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Job Year</th>
                    <th>Search By</th>
                    <th id="search_by_td_up">Please Enter Job No</th>
                    <th colspan="2">Date Range</th>
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">					
                        <input type="hidden" name="hide_po_id" id="hide_po_id" value="" />
                        <input type="hidden" name="hide_po_no" id="hide_po_no" value="" />
                        <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    </th> 
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">
                        	 <? 
								
								if($buyer>0) $buy_cond=" and a.id=$buyer";
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
							?>
                        </td>                 
                        <td align="center">	
                    	<?						
							echo create_drop_down( "cbo_job_year", 80, $year,"",0, "--Select--", date('Y'),'',0 );
						?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"",0, "--Select--", "2",$dd,0 );
						?>
                        </td>    
                       
                        </td> 
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                            <input type="text" style="width:80px;" class="datepicker"  name="txt_date_from" id="txt_date_from" readonly />   
                        </td>
                        <td align="center">
                            <input type="text" style="width:80px;" class="datepicker"  name="txt_date_to" id="txt_date_to" readonly />   
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+document.getElementById('cbo_job_year').value, 'style_popup_search_list_view', 'search_div', 'process_order_wise_rate_entry_for_pcs_rate_worker_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                   
            	</tbody>
                
                
           	</table>
               </tr>
                    <tr>
                    <td  align="center"  valign="middle">
                        <?=load_month_buttons(1);?>
                    </td>
                </tr>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>     
    <script>
        document.getElementById('cbo_company_id').value='<?=$company;?>';
    </script>      
    
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="style_popup_search_list_view")
{
  	// echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year,$txt_date_from,$txt_date_to,$job_year)=explode('**',$data);
	if($company_id==0)
	{
		echo "Please Select Company Name";
		die;
	}
	//echo $company_id."==".$buyer_id."==".$search_type."==".$search_value."==".$cbo_year;die;
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}
    else if($search_type==3 && $search_value!=''){
		$search_con=" and b.po_number like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($job_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$job_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$job_year";	
		}
	}
	else $year_cond="";
	
	if($db_type==2)
	{
		$group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		$group_field="group_concat(distinct b.po_number ) as po_number";
		$year_field="YEAR(a.insert_date)";
	}
    
    if($txt_date_from!='' && $txt_date_to!=''){ 
        if($db_type==0){
            
            $txt_date_from=change_date_format($txt_date_from);
            $txt_date_to=change_date_format($txt_date_to);
        }
        else
        {
            $txt_date_from=change_date_format($txt_date_from,'','',-1);
            $txt_date_to=change_date_format($txt_date_to,'','',-1);
        }
        $date_con=" and b.po_received_date BETWEEN '$txt_date_from' and '$txt_date_to'";   
    }
    else
    {
        $date_con="";   
    }
	$arr=array (2=>$company_arr,3=>$buyer_arr);
	$sql= "SELECT a.id as job_id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year , b.id as po_id,b.po_number,b.po_received_date
	from wo_po_details_master a,  wo_po_break_down b 
	where a.id=b.job_id and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con $date_con
	order by a.id desc";
	//  echo $sql;die;
	$rows=sql_select($sql);
	?>
    
    <table width="820" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="120">Buyer</th>
                <th width="50">Year</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
                <th>Po number</th> 
            </tr>
       </thead>
    </table>
    <div style="max-height:230px; overflow:auto;">
    <table width="800" border="1" rules="all" class="rpt_table" id="tbl_list_search">
     <? $rows=sql_select($sql);
         $i=1;
         foreach($rows as $data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
			?>
			<tr id="tr_<?=$i;?>" bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $i; ?>_<? echo $data[csf('po_id')]; ?>_<? echo $data[csf('job_id')]; ?>_<? echo $data[csf('po_number')]; ?>')" style="cursor:pointer;">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="120"><p><? echo $company_arr[$data[csf('company_name')]]; ?></p></td>
                <td width="120"><p><? echo $buyer_short_library[$data[csf('buyer_name')]]; ?></p></td>
                <td align="center" width="50"><p><? echo $data[csf('year')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('job_no_prefix_num')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
                <td><p><? echo $po_num; ?></p></td>
			</tr>
			<? 
			$i++; 
		} 
		?>
    </table>
    </div>
    <div style="width:100%">
		<div style="width:50%; float:left" align="left">
		    <input type="checkbox" name="check_all" id="check_all" onclick="check_all_data()"> Check / Uncheck All
		</div>
		<div style="width:50%; float:left" align="left">
		    <input type="button" name="close" id="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
		</div>
	</div>
    
	<script>setFilterGrid('tbl_list_search',-1)</script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <?
	
	//echo $sql;
	//echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	//echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}


if($action=="populate_price_rat_mst_form_data")
{
    $sqlResult =sql_select("SELECT a.id,a.system_no,c.company_id,c.rate_category,c.style_ref,c.process_id,c.uom,c.currency,c.rate,c.status,b.job_no,c.entry_date,c.id as dtls_id,c.po_ids,c.job_id from process_order_wise_rate_entry_mst a,process_order_wise_rate_entry_dtls c ,WO_PO_DETAILS_MASTER b where a.id=c.mst_id  and c.job_id=b.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$data");
    //  echo $sqlResult;die;

    $po_ids='';
    foreach($sqlResult as $v)
    {
        $po_ids .= ($po_ids=="") ? $v['PO_IDS'] : ",".$v['PO_IDS'];
    }
    $order_arr=return_library_array( "SELECT id, po_number from WO_PO_BREAK_DOWN where id in($po_ids)",'id','po_number');

    foreach($sqlResult as $result)
	{
        $po_arr = explode(",",$result['PO_IDS']);
        $po_name = '';
        foreach ($po_arr as $r) 
        {
            $po_name .= ($po_name=="") ? $order_arr[$r] : ",".$order_arr[$r];
        }

		echo "$('#update_id').val('".$result[csf('id')]."');\n";	
		echo "$('#dtls_id').val('".$result[csf('dtls_id')]."');\n";	
		echo "$('#txt_system_id').val('".$result[csf('system_no')]."');\n";	
		echo "$('#txt_date').val('".change_date_format($result[csf('entry_date')])."');\n";	
        echo "$('#cbo_company_id').val('".$result[csf('company_id')]."');\n";
        echo "$('#cbo_rate_category').val('".$result[csf('rate_category')]."');\n";
        echo "$('#txt_style_ref').val('".$po_name."');\n";
        echo "$('#cbo_process').val('".$result[csf('process_id')]."');\n";
        echo "$('#cbo_uom').val('".$result[csf('uom')]."');\n";
        echo "$('#cbo_currency').val('".$result[csf('currency')]."');\n";
        echo "$('#txt_exchange_rate').val('".$result[csf('rate')]."');\n";
        echo "$('#cbo_status').val('".$result[csf('status')]."');\n";
        echo "$('#hidden_job_id').val('".$result[csf('job_id')]."');\n";
        echo "$('#hidden_po_id').val('".$result[csf('po_ids')]."');\n";

    }    
}
if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    //print_r($process);die;
    extract(check_magic_quote_gpc( $process )); 
    
    if ($operation==0)  // Insert Here
    { 
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        
        
        $flag=1;
        if(str_replace("'","",$update_id)=="")
        {
            if($db_type==0) $year_cond="YEAR(insert_date)"; 
            else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
            else $year_cond="";//defined Later
            
            $id = return_next_id_by_sequence("PROCESS_ORDER_WISE_RATE_ENTRY_MST_SEQ", "process_order_wise_rate_entry_mst", $con);
        

            // master part--------------------------------------------------------------;
            $price_rate_bill_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'PRW', date("Y",time()), 5, "select sys_num_prefix, sys_num_prefix_no from process_order_wise_rate_entry_mst where company_id=$cbo_company_id and $year_cond=".date('Y',time())." order by id desc", "sys_num_prefix", "sys_num_prefix_no" ));            
            
            $field_array_mst="id,sys_num_prefix,sys_num_prefix_no,system_no,company_id,inserted_by,insert_date,status_active,is_deleted"; 

            $data_array_mst="(".$id.",'".$price_rate_bill_system_id[1]."',".$price_rate_bill_system_id[2].",'".$price_rate_bill_system_id[0]."',".$cbo_company_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";

            $system_no = $price_rate_bill_system_id[0];
        }

        // details part--------------------------------------------------------------;
        $id_dtls = return_next_id_by_sequence("process_order_wise_rate_entry_dtls_seq", "process_order_wise_rate_entry_dtls", $con);
        if(str_replace("'","",$update_id)!="")
        {
            $id = str_replace("'","",$update_id);
            $system_no = str_replace("'","",$txt_system_id);
        }
        
        $field_array_dtls="id, mst_id, company_id,rate_category, style_ref,job_id, po_ids,process_id,uom,currency,rate,entry_date,status,inserted_by, insert_date,status_active,is_deleted";

        $data_array_dtls="(".$id_dtls.",".$id.",".$cbo_company_id.",".$cbo_rate_category.",".$txt_style_ref.",".$hidden_job_id.",".$hidden_po_id.",".$cbo_process.",".$cbo_uom.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_date.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";


        
        //echo "10**".$data_array_dtls; die;
        
        //    echo "10** insert into process_order_wise_rate_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        $rID1=$rID2 = true;
        if(str_replace("'","",$update_id)=="")
        {
            $rID1=sql_insert("process_order_wise_rate_entry_mst",$field_array_mst,$data_array_mst,0);
        }
        $rID2=sql_insert("process_order_wise_rate_entry_dtls",$field_array_dtls,$data_array_dtls,0);

        // echo "10** ".$rID1."**".$rID2; die;
        
        //echo "10** ".$rID; die;
        if($db_type==0)
        {
            if($rID1 && $rID2)
            {
                mysql_query("COMMIT");  
                echo "0**".$id."**".$price_rate_bill_system_id[0]."**".str_replace("'", "", $upcharge)."**".str_replace("'", "", $discount);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".$rID1."**".$rID2;
                 //echo "10** insert into piece_rate_wo_urmi_dtls($field_array_dtls)values".$data_array_dtls;die;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID1 && $rID2)
            {
                oci_commit($con);  
                echo "0**".$id."**".$system_no."**".str_replace("'", "", $id_dtls);
            }
            else
            {
                oci_rollback($con);
                echo "10**".$rID1."**".$rID2;
                 //echo "10** insert into piece_rate_wo_urmi_dtls($field_array_dtls)values".$data_array_dtls;die;
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
       
        
        $update_id=str_replace("'","",$update_id);
        
        $field_array_up="company_id*updated_by*update_date";
        $data_array_up="".$cbo_company_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";   

      
        

        $field_array_up_dtls="company_id*rate_category*style_ref*process_id*uom*currency*rate*status*entry_date*updated_by*update_date";       
        $data_array_up_dtls="".$cbo_company_id."*".$cbo_rate_category."*".$txt_style_ref."*".$cbo_process."*".$cbo_uom."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_status."*".$txt_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";   
        //echo "10**insert into process_order_wise_rate_entry_mst (".$field_array_up.") values ".$data_array_up;die;  

           
        
        $rID1=sql_update("process_order_wise_rate_entry_mst",$field_array_up,$data_array_up,"id",$update_id,1);
        //    echo "10**".$rID1;die;
        $rID2=sql_update("process_order_wise_rate_entry_dtls",$field_array_up_dtls,$data_array_up_dtls,"id",$dtls_id,1);

        // echo "10**".$rID1."**".$rID2;die;
     
            
      

        if($db_type==0)
        {
            if($rID)
            {
                mysql_query("COMMIT");                
                echo "1**".str_replace("'", '', $update_id);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".$rID;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID1 && $rID2)
            {
                oci_commit($con);  
                echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**".str_replace("'", "", $id_dtls);
                //echo "0**".$id."**".$system_no."**".str_replace("'", "", $id_dtls);
            }
            else
            {
                oci_rollback($con);
                echo "10**".$rID1."**".$rID2;
            }
        }
        disconnect($con);
        die;    
    }    
    else if ($operation==2)   // Delete Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        $user=$_SESSION['logic_erp']['user_id'];
        $update_id=str_replace("'","",$update_id);
        $field_array_del="status_active*is_deleted*updated_by*update_date";       
        $data_array_del="0*1*$user*'$pc_date_time'";

        // $rID = sql_update("process_order_wise_rate_entry_mst",$field_array_del,$data_array_del,"id",$update_id,0);
        $rID=execute_query("UPDATE PROCESS_ORDER_WISE_RATE_ENTRY_DTLS set status_active=0, is_deleted=1,updated_by='$user',update_date='$pc_date_time' where id=$dtls_id and mst_id=$update_id" );
        // echo "10**UPDATE PROCESS_ORDER_WISE_RATE_ENTRY_DTLS set status_active=0, is_deleted=1,updated_by='$user',update_date='$pc_date_time' where id=$dtls_id and mst_id=$update_id".$rID;die;
        if($db_type==0)
        {
            if($rID)
            {
                mysql_query("COMMIT");  
                echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**".str_replace("'", "", $dtls_id);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".$rID;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID)
            {
                oci_commit($con);
                echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**".str_replace("'", "", $dtls_id);
            }
            else
            {
                oci_rollback($con);
                 echo "10**".$rID;
            }
        }
        disconnect($con);
        die;
    }
}

?>