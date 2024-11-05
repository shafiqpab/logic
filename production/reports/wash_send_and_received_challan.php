<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	Wash Send and Received Challan Report
Functionality	:	
JS Functions	:
Created by		:	Md Rakib Hasan Mondal
Creation date 	: 	14-August-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,1,1);
$company_arr = "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name";    

?> 
<script>

    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission = '<? echo $permission; ?>';
    var tableFilters =
            { 
                col_operation: 
                {
                    id: ["total_issue_qty"],
                    col: [14],
                    operation: ["sum"],
                    write_method: ["innerHTML"]
                }
            }
    function getLocationId() 
	{	 
        
		let company_id = document.getElementById('cbo_company_id').value; 
	    
        let data="action=load_drop_down_location&choosenCompany="+company_id;
        http.open("POST","requires/wash_send_and_received_challan_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data); 
        http.onreadystatechange = function(){ 
            if(http.readyState == 4) 
            {
                let response = trim(http.responseText);
                $('#location_td').html(response);
                set_multiselect('cbo_location_id','0','0','','0');
                setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');") ,3000)];
                getBuyer();
            }			 
        };     
	}
    function getBuyer() 
	{	 
		let company_id = document.getElementById('cbo_company_id').value; 

	    
        let data="action=load_drop_down_buyer&choosenCompany="+company_id;
        http.open("POST","requires/wash_send_and_received_challan_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data); 
        http.onreadystatechange = function(){
            if(http.readyState == 4) 
            {
                let response = trim(http.responseText);
                $('#buyer_td').html(response);
            }			 
        };     
	}
    function browseJobStyle(popupFor)
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_name").val();
		  
        let title = (popupFor == 1) ? 'Job No Search' : 'Style Search' ; 
		var page_link='requires/wash_send_and_received_challan_controller.php?action=job_style_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&popupFor='+popupFor;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var popupFor=this.contentDoc.getElementById("hide_popup_for").value;
            $('#txt_job_id').val(job_id);
            $('#txt_job_no').val(job_no);   
			
		}
	} 
    
    function fn_report_generated(type)
    {
        if ($('#txt_job_no').val()) 
        {
            if (form_validation('cbo_company_id*cbo_report_basis*cbo_source','Company Name*Basis*Source')==false)
            {
                return;
            }
        }
        else
        {
            if (form_validation('cbo_company_id*cbo_report_basis*cbo_source*txt_date_from*txt_date_to','Company Name*Basis*Source*From Date*To Date*')==false)
            {
                return;
            }
        }
        
                   
        var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_year*cbo_buyer_name*cbo_report_basis*cbo_source*cbo_sending_comp*cbo_emb_company*cbo_wash_location*txt_job_no*txt_date_from*txt_date_to',"../../");
        freeze_window(3);
        http.open("POST","requires/wash_send_and_received_challan_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse; 
    } 
    function fn_report_generated_reponse()
    {
        if(http.readyState == 4) 
        {
            show_msg('3'); 
            var reponse=trim(http.responseText).split("####");
            $('#report_container2').html(reponse[0]);
            // alert(reponse[2]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="fn_print_btn()" value="Print" name="Print" class="formbutton" style="width:100px"/>';
         
            setFilterGrid("table_body",-1,tableFilters); 
            release_freezing();
        }
        
    }
    function fnc_checkbox_check(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");

		var issue_to=$('#issue_to_'+rowNo).val();
        var lc_to=$('#lc_comp_'+rowNo).val();
		var wash_to=$('#wash_comp_'+rowNo).val();

		if(isChecked==true)
		{
			var tot_row=$('#table_body tr').length-1;
			for(var i=1; i<=tot_row; i++)
			{ 
				if(i!=rowNo)
				{
					try 
					{
						if ($('#tbl_'+i).is(":checked"))
						{
							var issue_toCurrent=$('#issue_to_'+i).val();
							var lc_toCurrent=$('#lc_comp_'+i).val();
							var wash_toCurrent=$('#wash_comp_'+i).val();
							if( (issue_to != issue_toCurrent) || (lc_to != lc_toCurrent) || (wash_to != wash_toCurrent) )					
							{
                                let basis = $('#cbo_report_basis').val();
                                //let comp = basis==2 ? "Sending Company" : "Receiving Company";
                                let comp = '';
								alert("Please Select Same LC, Wash and Sending Company"+comp);
								$('#tbl_'+rowNo).attr('checked',false);
								return;
							}
						}
					}
					catch(e){}
				}
			}
		}
	}
    function fn_print_btn() 
	{
        var sys_ids = ""; var total_tr=$('#table_body tr').length;
        for(i=1; i<total_tr; i++)
        {
            try 
            {
                if ($('#tbl_'+i).is(":checked"))
                {
                    sys_id = $('#sys_id_'+i).val();
                    if(sys_ids=="") sys_ids= sys_id; else sys_ids +='_'+sys_id;
                }
            }
            catch(e){}
        }
        if(sys_ids=="")
        {
            alert("Please Select At Least One Item");
            return;
        }
        freeze_window(3);
        var report_title="Wash Delivery Challan";
        print_report( $('#cbo_report_basis').val()+'*'+sys_ids+'*'+report_title+'*'+$('#txt_print_date').val()+'*'+$('#cbo_emb_company').val()+'*'+$('#cbo_wash_location').val(), "delivery_challan_print", "requires/wash_send_and_received_challan_controller" );
        release_freezing(); 
        return; 
	}
    
</script>         
                          
</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:100%;" align="center">   
        <? echo load_freeze_divs ("../../",'');  ?>     
        <fieldset style="width:1605px;">
            <legend>Search Panel</legend> 
            <table class="rpt_table" width="1605px" cellpadding="0" cellspacing="0" border="1" align="center">
                <thead> 
                    <tr>
                        <th> Date</th>
                        <td>
                            <input type="text" name="txt_print_date" id="txt_print_date" class="datepicker" style="width:140px" value="<?= date('d-m-Y') ?>"/>
                        </td>
                        
                    </tr>                   
                    <tr>
                        <th width="150" class="must_entry_caption">Lc Company</th>
                        <th width="150">Location</th>
                        <th width="60">Year</th>
                        <th width="150">Buyer</th> 
                        <th width="110" class="must_entry_caption">Basis</th> 
                        <th width="110" class="must_entry_caption">Source</th>
                        <th width="150">Sending/Receiving Company</th>
                        <th width="150"> Wash Company</th>
                        <th width="150">Location</th> 
                        <th width="150">Job No</th> 
                        <th width="280" id="search_text_td"  class="must_entry_caption">Date</th>
                        <th width="100"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                </thead>
                <tbody> 
                    <tr >
                        <td align="center" id="td_company"> 
                            <?
                                echo create_drop_down( "cbo_company_id", 150, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                            ?>
                        </td>                  
                        <td align="center" id="location_td"> 
                            <?
                                echo create_drop_down( "cbo_location_id", 150, $blank_array,"","", "-- Select location --", "", "" ); 
                            ?>
                        </td>
                        <td width="60" id="year_td">
                            <?
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "-- All --", date('Y'), "",0,"" );
                            ?>
                        </td>
                        <td width="150" id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "", 1, "" );
                            ?>
                        </td>   
                        <td width="100">
                            <?
                                $basis_arr = array(2=>"Issue",3=>"Receive");
                                echo create_drop_down( "cbo_report_basis", 110, $basis_arr,"", 1, "--Select --","", "",0,"" );
                            ?>
                        </td>   
                        <td width="110">
                            <? echo create_drop_down( "cbo_source", 110, $knitting_source,"", 1, "-- Select Source --", 1, "load_drop_down( 'requires/wash_send_and_received_challan_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_wash_company', 'emb_company_td' );", 0, '1,3' ); ?>
                        </td>
                        <td width="110" align="center" >				
                            <? echo create_drop_down( "cbo_sending_comp",150,$company_arr,"id,company_name", 1, "-- Select Company --", $selected, "" );?>
                        </td> 
                        <td id="emb_company_td" width="150">
                            <? echo create_drop_down( "cbo_emb_company", 150,$company_arr,"id,company_name", 1, "-- Select Company--", $selected, "load_drop_down( 'requires/wash_send_and_received_challan_controller', this.value, 'load_drop_down_wash_location', 'wash_location_td' );" ); ?>
                        </td>
                        <td id="wash_location_td" >
                            <? echo create_drop_down( "cbo_wash_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, ""); ?>
                        </td>  
                        <td>
                            <input style="width:140px;" type="text"  onDblClick="browseJobStyle(1)" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_job_no" id="txt_job_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                            <input  type="hidden"  name="txt_job_id" id="txt_job_id"  />

                        </td> 
                        <td width="140" align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px"/>                                             
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px"/>
                        </td>                
                        <td width="100">
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="12"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script>
    set_multiselect('cbo_company_id','0','0','','0'); 
	set_multiselect('cbo_location_id','0','0','','0');  
	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_id,'0');getLocationId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
