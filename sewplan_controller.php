<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

//************************************ Start*************************************************
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","", 1, 1, $unicode);
	extract($_REQUEST);
	$line_array=return_library_array("select id,line_name from lib_sewing_line where id<31 order by id ", "id","line_name",1);
	$pdate=substr($pdate,0,2)."-".substr($pdate,2,2)."-".substr($pdate,4,4);
	
	$complexity_level=array(1=>"Basic",2=>"Simply Complex", 3=>"Highly Complex");
							 
	$complexity_level_data[1]['fdout']=1000;
	$complexity_level_data[1]['increment']=100;
	$complexity_level_data[1]['target']=1200;
	$complexity_level_data[2]['fdout']=800;
	$complexity_level_data[2]['increment']=100;
	$complexity_level_data[2]['target']=1200;
	$complexity_level_data[3]['fdout']=600;
	$complexity_level_data[3]['increment']=100;
	$complexity_level_data[3]['target']=1200; complexity_levels
	
							 
?>
     
	<script>
	<?
	$line_array_js= json_encode($line_array); 
	echo "var line_array = ". $line_array_js . ";\n";
	
	$complexity_levels= json_encode($complexity_level_data); 
	echo "var complexity_levels = ". $complexity_levels . ";\n";
	
	?>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( job_no )
	{
		$('#search_div').css('visibility','collapse');
		$('#search_panel').css('visibility','collapse');
		$('#search_div_line').css('visibility','visible');
		var jdata=job_no.split("_"); //job_no,id,po_quantity,shipment_date,po_number,shipment_date,style_ref_no
		$('#order').html(jdata[4]);
		$('#shipdate').html(jdata[5]);
		$('#orderqnty').html(jdata[2]);
		//$('#search_div_line').html(jdata[4]);
	 
						
		 document.getElementById('selected_job').value=job_no;
		//parent.emailwindow.hide();
	}
	function show_hide( fm )
	{
		if(fm==1)
		{
			$('#search_div_line').css('visibility','collapse');
			$('#search_div_lag').css('visibility','visible');
		}
		else if(fm==2)
		{
			var orderinfo= document.getElementById('selected_job').value;
			var planData="";
			var rows =document.getElementById('tbl_line').rows.length-2;
			for( var k=1; k<rows; k++ )
			{
				if(planData!="") planData=planData+"****";
				planData=planData+$('#cbo_line_selection__'+k).val()+"**"+$('#txt_line_date__'+k).html()+"**"+$('#txt_plan_qnty__'+k).html();
			}
			
			planData=orderinfo+"______"+planData+"______"+$('#cbo_complexity_selection').val( )+"**"+$('#txt_first_day').val( )+"**"+$('#txt_increment').val( )+"**"+$('#txt_target').val( ); 
			document.getElementById('selected_job').value=planData;
			parent.emailwindow.hide();
			//alert(planData);
			/*jQuery("#tbl_line tbody tr").each(function(e) {
				jQuery(this +" td").each(function(e) {
					alert($(this).html());
				});
				alert('asdasd'); 
			});*/
			
		}
	}
	function add_line()
	{
		var rowCount =document.getElementById('tbl_line').rows.length-2;
		if (rowCount%2==0)  
			var bgcolor="#E9F3FF";
		else
			var bgcolor="#FFFFFF";
		 var row_idss= $('#up_row_id').val();
		 if ( row_idss=="" ) var rowCount=(rowCount*1); else rowCount=row_idss;
		
		var new_html='<tr  bgcolor="'+ bgcolor +'" id="row_' + rowCount + '" onclick="set_update_row(' + rowCount + ',0)" style="cursor:pointer">'
					+ '<td id="txt_line' + rowCount + '" width="20">'+ line_array[$('#cbo_line_selection').val()] +'</td>'	
					+ '<td id="txt_line_date__' + rowCount + '" width="100">'+ $('#txt_line_date').val() +'</td>'
					+ '<td id="txt_plan_qnty__' + rowCount + '" width="100">'+ $('#txt_plan_qnty').val() +'</td>'
					+'<td id="blank_' + rowCount + ' onclick="set_update_row(' + rowCount + ',1)"><input type="button" name="" value="Clear"  id="btn_clear_' + rowCount + '"  class="formbutton" onclick="set_update_row(' + rowCount + ',1)"/><input type="hidden" id="cbo_line_selection__' + rowCount + '" value="'+$('#cbo_line_selection').val()+'" /></td></tr>';
			if( row_idss=="")			
					$("#tbl_line tbody").append(new_html);
			else
				$('#row_' + rowCount).replaceWith(new_html);
				$('#up_row_id').val('');
	}
	
	function set_update_row(id, is_clear)
	{ 
		if(is_clear==0)
		{
			$('#up_row_id').val(id);
			$("#row_"+id +" td").each(function() {
				var tdid2=$(this).attr('id');
				var tdid=tdid2.split("__");
				
				if(!tdid[1] && tdid[1]!='undefined')
					var d=1;//alert("d=="+tdid[1]);
				else
				{
					$('#'+tdid[0]).val($(this).html());
				}
				// $(this).html('');
			});
			$('#cbo_line_selection').val($('#cbo_line_selection__'+id).val());
			
		}
		else
		{
			$("#row_"+id +" td").each(function() {
				 $(this).html('');
			});
			$('#up_row_id').val('');
		}
	}
	
	function open_back( fm )
	{
		if(fm==1)
		{
			$('#search_div').css('visibility','visible');
			$('#search_panel').css('visibility','visible');
			$('#search_div_line').css('visibility','collapse');
		}
		else if(fm==2)
		{
			$('#search_div_line').css('visibility','visible');
			//$('#').css('visibility','visible');
			$('#search_div_lag').css('visibility','collapse');
		}
	}
	function fill_complexity(vid)
	{  
		$('#txt_first_day').val( complexity_levels[vid]['fdout'] );
		$('#txt_increment').val( complexity_levels[vid]['increment'] );
		$('#txt_target').val( complexity_levels[vid]['target'] ); 
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="850" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            <div id="search_panel">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        <thead>
                         <th width="150" colspan="3"> </th>
                        	<th>
                              <?
                               echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th width="150" colspan="2"> </th>
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Job No</th>
                        <th width="120">Order No</th>
                        <th width="200">Ship Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'woven_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value, 'create_po_search_list_view', 'search_div', 'sewplan_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
                
                <tr>
                    <td  align="center" colspan="6" height="40" valign="middle">
                     <? 
                    echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                    ?>
                    <? echo load_month_buttons();  ?>
                    </td>
                 </tr>
             </table>
             </div>
          </td>
        </tr>
        
        <tr>
            <td align="center" valign="top" id=""> 
				<div style="visibility:visible" id="search_div"></div>
                <div style="visibility:collapse" id="search_div_line">
                	<table width="630">
                    	<tr>
                        	<td width="100">Order No</td><td width="130" id="order"></td>
                            <td width="100">Shipment Date</td><td width="100" id="shipdate"></td>
                            <td width="100">Order Quantity</td><td id="orderqnty"></td>
                        </tr>
                    </table>
                	<table width="350" class="rpt_table" id="tbl_line">
                    	<thead>
                         
                        <tr>
                            <th width="120"> Line No</th>
                             <th width="90">Start Date</th>
                             <th width="90">Quantity</th>
                             <th>
                             	 <input type="hidden" id="up_row_id" />
                                 <input type="button" class="formbutton" style="width:80px" value="Go Back" onClick="open_back(1)" /> 
                            </th>
                        </tr>
                    	<tr>
                             <th width="120"><? echo create_drop_down( "cbo_line_selection", 110, $line_array, "", 1, "-- Select --", $pline, "",0 ); ?></th>
                             <th width="90"><input type="text" class="datepicker" value="<? echo $pdate; ?>" id="txt_line_date" style="width:80px" /></th>
                             <th width="90"><input type="text" class="text_boxes_numeric" id="txt_plan_qnty" style="width:80px" /></th>
                             <th>
                             	<input type="button" class="formbutton" style="width:80px" value="Add" onClick="add_line()" /> 
                            </th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                        	 
                            	<th colspan="2" align="left">
                                <input type="button" class="formbutton" style="width:80px" value="Close" onClick="show_hide(1)" /></th>
                                <th colspan="2" align="left"></th>
                             
                        </tfoot>
                        
                    </table>
                
                
                </div>
                <div style="visibility:collapse" id="search_div_lag">
                	<table width="450" class="rpt_table" id="tbl_lag">
                    	<thead>
                        <tr>
                            <th width="130">Complexity Level</th>
                             <th width="120">First Day Output</th>
                             <th width="90">Increment</th>
                             <th width="90">Target</th>
                             <th><input type="button" class="formbutton" style="width:80px" value="Go Back" onClick="open_back(2)" /> </th>
                        </tr>
                    	<tr>
                             <th width="120">
							 <? 
							 echo create_drop_down( "cbo_complexity_selection", 110, $complexity_level, "", 1, "-- Select --",'', "fill_complexity(this.value)",0 );  
							 ?></th>
                             <th width="90"><input type="text" class="text_boxes_numeric" id="txt_first_day" style="width:80px" /></th>
                             <th width="90"><input type="text" class="text_boxes_numeric" id="txt_increment" style="width:80px" /></th>
                             <th width="90"><input type="text" class="text_boxes_numeric" id="txt_target" style="width:80px" /></th>
                             <th>
                             	<input type="button" class="formbutton" style="width:80px" value="Add" onClick="add_line()" /> 
                            </th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                        	 
                            	<th colspan="4" align="center"><input type="button" class="formbutton" style="width:80px" value="Close" onClick="show_hide(2)" /></th>
                            
                        </tfoot>
                        
                    </table>
                
                
                </div>
            </td>
        </tr>
    </table>    
     
    </form>
   </div>
</body>           
<script src="includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	
	
	if($db_type==0)
	{
	$year_cond=" and SUBSTRING_INDEX(a.`insert_date`, '-', 1)=$data[7]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$order_cond="";
	$job_cond=""; 
	if($data[8]==1)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		  if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond=""; 
		}
	
	if($data[8]==4 || $data[8]==0)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		}
	
	if($data[8]==2)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		}
	
	if($data[8]==3)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		}
	
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	if ($data[2]==0)
	{
		if($db_type==0)
		{
	 		$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond order by a.job_no";
		}
	 	if($db_type==2)
		{
	 		$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,to_char(a.insert_date,'YYYY') as year,b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=2 and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond order by a.job_no";
		}
		//echo $sql;die;
		//echo $sql;
		 echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature", "50,60,120,100,100,90,90,90,80,80","1000","320",0, $sql , "js_set_value", "job_no,id,po_quantity,shipment_date,po_number,shipment_date,style_ref_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,garments_nature", "",'','0,0,0,0,0,1,0,1,3,0');
	}
	else
	{
		$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category);
		if($db_type==0)
		{
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1   and a.is_deleted=0 $company $buyer order by a.job_no";
		}
		if($db_type==2)
		{
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1   and a.is_deleted=0 $company $buyer order by a.job_no";
		}
		
		
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,120,100,100,90","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
} 


?>