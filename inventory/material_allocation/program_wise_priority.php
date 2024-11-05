<?
/*-------------------------------------------- Comments
Purpose			: 	Barcode Issue To Finishing		
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	12-04-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

	require_once('../../includes/common.php');
	extract($_REQUEST);

	$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Program Wise Priority", "../../", 1, 1, $unicode, '', '');
    //print_r($_SESSION['logic_erp']['data_arr'][478]); //die;
	?>
<script>

		if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
		var permission = '<? echo $permission; ?>';

    <?
    $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][478] );
    echo "var field_level_data= ". $data_arr . ";\n";
    
    ?>

        function generate_report(type){
           
            var cbo_company_id=document.getElementById('cbo_company_id').value;
            var cbo_fso_no_txt=document.getElementById('cbo_fso_no_txt').value;
            var cbo_knitting_source=document.getElementById('cbo_knitting_source').value;
            var cbo_floor_id=document.getElementById('cbo_floor_id').value;
           
            var hide_job_id=document.getElementById('hide_job_id').value;
            var txt_date_from=document.getElementById('txt_date_from').value;
            var txt_date_to=document.getElementById('txt_date_to').value;
            var hide_cbo_mc_id=document.getElementById('hide_cbo_mc_id').value;

            if(hide_job_id=="" || cbo_fso_no_txt==""){
                if ( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*Date*Date')==false )
                {
                    return;
                }
            }else{
                if ( form_validation('cbo_company_id','Company')==false )
                {
                    return;
                }
            }

           
           
            
            var data='action=generate_report&type='+type+get_submitted_data_string('cbo_company_id*cbo_knitting_source*cbo_floor_id*cbo_fso_no_txt*hide_job_id*cbo_mc_no_txt*txt_date_from*txt_date_to*cbo_status*hide_cbo_mc_id','../../');
            freeze_window(5);
            http.open("POST","requires/program_wise_priority_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_show_details_reponse;  
        }
       

       
        function fnc_show_details_reponse(){
            if(http.readyState == 4) 
            {
                var response=trim(http.responseText);
                //alert(response);
                $('#report_container').html(response);
                set_all_onclick();
                show_msg('18');
                release_freezing();
                
               
            }
        }
        //  function generate_report2(company_id, program_id,format_id="") {
           
        //     var path = '../';
            
        //     if(format_id==273)
        //     {
        //         print_report(company_id + '*' + program_id + '*' + path, "prog_info_print", "../../../prod_planning/requires/planning_info_entry_for_sales_order_controller");
        //     }else {
        //         print_report(company_id + '*' + program_id + '*' + path, "print", "requires/program_wise_priority_controller");
        //     }
            
        // }
        function generate_report2(company_id, program_id, within_group) {
            var path = '../../../';
            print_report(program_id + '**0**' + path + '**' + within_group, "requisition_print_two", "requires/program_wise_priority_controller");
        }

    function openmypage_job() 
    {
        if (form_validation('cbo_company_id', 'Company Name') == false) {
            return;
        }
        var companyID = $("#cbo_company_id").val();
        var cbo_knitting_source = $("#cbo_knitting_source").val();
        
        
        var page_link = 'requires/program_wise_priority_controller.php?action=style_ref_search_popup&companyID=' + companyID + '&cbo_knitting_source=' + cbo_knitting_source ;
        ;
        var title = 'Style Ref./ Job No. Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=400px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var job_no = this.contentDoc.getElementById("hide_job_no").value;
            var job_id = this.contentDoc.getElementById("hide_job_id").value;

            $('#cbo_fso_no_txt').val(job_no);
            $('#hide_job_id').val(job_id);
        }
    }

    function openmypage_machine_no() 
    {
        if (form_validation('cbo_company_id*cbo_floor_id', 'Company Name*Floor') == false) {
            return;
        }

        var companyID = $("#cbo_company_id").val();
        var cbo_floor_id = $("#cbo_floor_id").val();
        var page_link = 'requires/program_wise_priority_controller.php?action=machine_no_search_popup&company_id=' + companyID + '&floor_id=' + cbo_floor_id;
        var title = 'Machine No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var order_no = this.contentDoc.getElementById("hide_order_no").value;
            var order_id = this.contentDoc.getElementById("hide_order_id").value;

            $('#cbo_mc_no_txt').val(order_no);
            $('#hide_cbo_mc_id').val(order_id);
        }
    }

      
        
</script>
</head>


<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
         <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1"> 
         <h3 style="width:1220px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:1320px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th>Company Name</th>
                            <th>Knitting Source</th>
                            <th>Floor</th>
                            <th>M/C No</th>
                            <th>FSO NO</th>
                            <th>Status</th>
                            <th class="must_entry_caption">Program Date Range</th>
                            <th>
                                <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container','','','')" class="formbutton" style="width:50px" />

                            </th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                 <td>
                                  <?
                                    echo create_drop_down("cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0", "id,company_name", 1, "--Select--", 0, "load_drop_down( 'requires/program_wise_priority_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_floor', 'floor_td' )", 0);//
                                    ?>
                                </td>
                                <td>
                                 <?
                                     echo create_drop_down( "cbo_knitting_source", 130, $knitting_source,"", 1, "-- Select Source --", $selected, "", 0, '1,3' );
                                ?>
                                </td>
                               

                                <td id="floor_td">
                                     <?
                                        $arr=array();
                                        echo create_drop_down("cbo_floor_id", 130, $arr, "", 1, "-- Select Floor --", 0, "", 1);
                                    ?>
                                </td>
                                <td> 
                                      <input type="text" name="cbo_mc_no_txt" id="cbo_mc_no_txt" class="text_boxes" placeholder="Browse M/C NO"
                                       onDblClick="openmypage_machine_no();" 
                                      >
                                      <input type="hidden" name="hide_cbo_mc_id" id="hide_cbo_mc_id">
                                </td>
                               
                                <td >
                                   
                                      <input type="text" name="cbo_fso_no_txt" id="cbo_fso_no_txt" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_job();" autocomplete="off" readonly>
                                    <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                                
                                </td>
                                     
                               <td>
                                     <?
                                     $status=array(1=>'Waiting',
                                            2=>'Running',
                                            4=>'Closed',
                                            3=>'Complete');
                                     echo create_drop_down( "cbo_status", 130,$status ,"", 1, "-- Select --", $selected, "", 0  );
                                ?>
                               </td>
                              
                               
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                                    style="width:70px" readonly>To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                                    readonly>
                                </td>
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="generate_report(1)" />
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                    <table>
                <tr>
                    <td colspan="9">
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table> 
            <br />
                </fieldset>
            </div>
            <div id="report_container"></div>
        </form>
    </div>
    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>