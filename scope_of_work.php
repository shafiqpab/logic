
<?
//----------------------------------------------------------------------------------------------------------------
//session_start();
//if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
//require_once('../../includes/common.php');
//extract($_REQUEST);   //output 1
//$_SESSION['page_permission']=$permission;     //output  1_1_1_1
//echo $_SESSION['logic_crm']['user_id']; output  2
//----------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Page setup","../../",1 ,1 ,$unicode,1,'','' );
?>

<style>
    .main_div { border: 1px solid #000; width: 900px; margin-left: 90px; margin-top: 50px; }
    .div1 { border-bottom: 1px solid #000; min-height: 100px; } 
    .div1_1{float: left; margin-top: 5px;} 
    .div1_2{text-align: center;} 
    .head_p1{ font-size: 35px;font-weight: bold; }
    .head_p2{font-weight: bold;font-size: 12px;}
    .head_p3{ font-size: 25px;font-weight: bold;}
    .div2{ margin-top: 20px; margin-left: 5px;} 
    .div2_p1 {font-size: 18px; text-decoration: underline; font-weight: bold; margin-bottom: 10px;}
    .parag{font-size: 15px; margin-bottom: 5px;}
    .div2_p2 { margin-top: 20px; font-size: 15px; } 
    .div2_p3 {font-size: 15px; margin-top: 15px; margin-bottom: 20px; }
    .div3{margin-left:5px;} 
    .div3_p1{font-size: 18px; text-decoration: underline; font-weight: bold; margin-bottom: 10px;} 
    .div3_p2{} 
    .div3_p3 {font-size: 15px; margin-top: 20px; margin-bottom: 25px; }
    .div4{} 
    table, td{border: 1px dotted black; border-left: none; border-collapse: collapse; text-align: left; font-size: 15px; padding: 5px; }
    .div5{border-top: 1px solid #000; min-height: 50px;} 
    .div5_1 {min-height: 50px; width: 50%; border-right: 1px solid #000; float: left; }
    .div5_p1{font-size: 15px; margin-left: 5px; padding-top: 13px;}
    .div5_2{min-height: 50px; width: 49%;float: right;padding-top: 13px;  margin-right: 5px; } 
    .div5_p2{font-size: 15px;text-align: right;} 
</style>
</head>

<body>
	<div class="main_div">
        <div class="div1">
            <div class="div1_1">
                <img src="../../css/rimages/picture.png" width="105" height="80"/>
            </div>
            <div class="div1_2">
                <p class="head_p1">SONIA & SWEATERS LTD</p>
                <p class="head_p2">PLOT NO: 604, KONDOLBAGH, TAIBPUR, ASHULIA ROAD, SAVAR, DHAKA - 1341, TEL : +880-2-7792471</p>
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
            <p class="parag">Co-efficient of Friction : < 0.20</p>

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
            <table cellpadding="0" cellspacing="0">
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
                    <td rowspan="2">Colour Fastness to Washing</td>
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
            <div class="div5_2"">
                <p class="div5_p2">Approved By: Mahabubur Rahman</p>
            </div>                       
        </div>     
    </div>                     

</body>
</html>

