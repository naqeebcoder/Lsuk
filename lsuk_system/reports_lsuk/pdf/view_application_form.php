<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
error_reporting(0);
include '../../db.php';
include_once '../../class.php';
$view_id=$_GET['view_id'];
$table='interpreter_reg';
$row=$acttObj->read_specific("*","$table","id=".$view_id);
$name=$row['name'];$email=$row['email'];$contactNo=$row['contactNo'];$contactNo2=$row['contactNo2'];
$rph=$row['rph'];$interp=$row['interp'];$telep=$row['telep'];$trans=$row['trans'];$gender=$row['gender'];
$city=$row['city'];$interp_code=$row['code'];$applicationForm=$row['applicationForm'];
$agreement=$row['agreement'];$crbDbs=$row['crbDbs'];$identityDocument=$row['identityDocument'];
$nin=$row['nin'];$cv=$row['cv'];$dps=$row['dps'];$anyOther=$row['anyOther'];$anyCertificate=$row['anyCertificate'];
$rpm=$row['rpm'];$rpu=$row['rpu'];$ni=$row['ni'];$buildingName=$row['buildingName'];$line1=$row['line1'];$line2=$row['line2'];$line3=$row['line3'];
$postCode=$row['postCode'];$bnakName=$row['bnakName'];$acName=$row['acName'];$acntCode=$row['acntCode'];$acNo=$row['acNo'];$dob=$row['dob'];
$dated=$row['dated'];$interp=$row['interp'];$telep=$row['telep'];$trans=$row['trans'];$dbs_file=$row['dbs_file'];$id_doc_file=$row['id_doc_file'];
$application_file=$row['applicationForm_file'];$agreement_file=$row['agreement_file'];$utr=$row['ni'];$uk_citizen=$row['uk_citizen']==0?'No':'Yes';
$work_evid_file=$row['work_evid_file'];$extra_data=json_decode($row['extra_data']);
if($work_evid_file){
    $work_evid_issue_date=$row['work_evid_issue_date'];
    $work_evid_expiry_date=$row['work_evid_expiry_date'];
}
$array_experties="";
if($interp=="Yes"){$array_experties.='<span style="font-size:12px;background-color:#54b654;"> Face To Face </span> ';}
if($telep=="Yes"){$array_experties.='<span style="font-size:12px;background-color:#337ab7;color:white;"> Telephone </span> ';}
if($trans=="Yes"){$array_experties.='<span style="font-size:12px;background-color:#8d8d8dba;color:white;"> Translation </span> ';}
$mndy=$row['mndy'];
$mndy_time=$row['mndy_time']=="00:00:00"?"Fulltime":$row['mndy_time'];
$mndy_to=$row['mndy_to']=="00:00:00"?"Fulltime":$row['mndy_to'];
$tsdy=$row['tsdy'];
$tsdy_time=$row['tsdy_time']=="00:00:00"?"Fulltime":$row['tsdy_time'];
$tsdy_to=$row['tsdy_to']=="00:00:00"?"Fulltime":$row['tsdy_to'];
$wdnsdy=$row['wdnsdy'];
$wdnsdy_time=$row['wdnsdy_time']=="00:00:00"?"Fulltime":$row['wdnsdy_time'];
$wdnsdy_to=$row['wdnsdy_to']=="00:00:00"?"Fulltime":$row['wdnsdy_to'];
$thsdy=$row['thsdy'];
$thsdy_time=$row['thsdy_time']=="00:00:00"?"Fulltime":$row['thsdy_time'];
$thsdy_to=$row['thsdy_to']=="00:00:00"?"Fulltime":$row['thsdy_to'];
$frdy=$row['frdy'];
$frdy_time=$row['frdy_time']=="00:00:00"?"Fulltime":$row['frdy_time'];
$frdy_to=$row['frdy_to']=="00:00:00"?"Fulltime":$row['frdy_to'];
$stdy=$row['stdy'];
$stdy_time=$row['stdy_time']=="00:00:00"?"Fulltime":$row['stdy_time'];
$stdy_to=$row['stdy_to']=="00:00:00"?"Fulltime":$row['stdy_to'];
$sndy=$row['sndy'];
$sndy_time=$row['sndy_time']=="00:00:00"?"Fulltime":$row['sndy_time'];
$sndy_to=$row['sndy_to']=="00:00:00"?"Fulltime":$row['sndy_to'];
$week_remarks=$row['week_remarks'];
$actnow=$row['actnow'];
$actnow_time=$row['actnow_time']=="0000-00-00"||$row['actnow_time']=="1001-01-01"?"All Day":$row['actnow_time'];
$actnow_to=$row['actnow_to']=="0000-00-00"||$row['actnow_to']=="1001-01-01"?"All Day":$row['actnow_to'];
$query_lang="SELECT id,lang,level FROM interp_lang where code='id-".$view_id."' ORDER BY lang ASC";
$result_lang = mysqli_query($con,$query_lang);
if(mysqli_num_rows($result_lang)==0){
    $append_languages='<span class="badge badge-primary">No Languages Currently!</span>';
}else{
    $level = array("1"=>"Native", "2"=>"Fluent", "3"=>"Intermediate", "4"=>"Basic");
    while($row_lang = mysqli_fetch_assoc($result_lang)){
        $append_languages.='<span style="font-size:12px;"> ('.$row_lang['lang'].' | '.$level[$row_lang['level']].') </span> ,';
    }
}
$ref_data=$acttObj->read_all("*","int_references","int_id=".$view_id);
if($ref_data->num_rows>0){
    $append_ref='<tr><td colspan="4" align="center" style="color: #fff;background-color: #337ab7;font-size: 18px;">REFERENCES</td></tr>';
    $ref_counter=0;
    while($row_ref=$ref_data->fetch_assoc()){
        $ref_counter++;
        $append_ref.='<tr><td colspan="4" align="center" style="font-size: 18px;">Reference '.$ref_counter.' Details</td></tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Full Name (Relationship)</td>
        <td style="border: 1px solid grey;padding:5px;">'.$row_ref['name'].' ('.$row_ref['relation'].')</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Company</td>
        <td style="border: 1px solid grey;padding:5px;">'.$row_ref['company'].'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Phone</td>
        <td style="border: 1px solid grey;padding:5px;">'.$row_ref['phone'].'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Email</td>
        <td style="border: 1px solid grey;padding:5px;">'.$row_ref['email'].'</td>
        </tr>';
    }
}
require_once 'tcpdf_include.php';
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetSubject('TCPDF Tutorial');
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once dirname(__FILE__) . '/lang/eng.php';
    $pdf->setLanguageArray($l);
}
$pdf->SetFont('helvetica', 'B', 12);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);

$tbl='<style type="text/css">
        table.myTable{border-collapse: collapse;}
        table.myTable td, table.myTable th {border: 1px solid grey;padding: 5px;}
        </style>
        <table class="myTable" width="100%" cellpadding="10">
        <tr><td colspan="4" align="center" style="color: #fff;background-color: #337ab7;font-size: 18px;">PERSONAL DETAILS</td></tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Full Name</td>
        <td colspan="3" style="border: 1px solid grey;padding:5px;">'.$name.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Date of Birth</td>
        <td style="border: 1px solid grey;padding:5px;">'.$dob.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Gender</td>
        <td style="border: 1px solid grey;padding:5px;">'.$gender.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Contact Number</td>
        <td style="border: 1px solid grey;padding:5px;">'.$contactNo.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Mobile Number</td>
        <td style="border: 1px solid grey;padding:5px;">'.$contactNo2.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Email</td>
        <td style="border: 1px solid grey;padding:5px;">'.$email.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">NI / UTR #</td>
        <td style="border: 1px solid grey;padding:5px;">'.$utr.'</td>
        </tr>
        <tr>
        <td colspan="1" style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Working As</td>
        <td colspan="3" style="border: 1px solid grey;padding:5px;">'.$array_experties.'</td>
        </tr>
        <tr><td colspan="4" align="center" style="color: #fff;background-color: #337ab7;font-size: 18px;">ADDRESS DETAILS</td></tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Building Name</td>
        <td style="border: 1px solid grey;padding:5px;">'.$buildingName.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Line 1</td>
        <td style="border: 1px solid grey;padding:5px;">'.$line1.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Line 2</td>
        <td style="border: 1px solid grey;padding:5px;">'.$line2.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Line 3</td>
        <td style="border: 1px solid grey;padding:5px;">'.$line3.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">City</td>
        <td style="border: 1px solid grey;padding:5px;">'.$city.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Post Code</td>
        <td style="border: 1px solid grey;padding:5px;">'.$postCode.'</td>
        </tr>
        <tr><td colspan="4" align="center" style="color: #fff;background-color: #337ab7;font-size: 18px;">WORK AVAILABILITY</td></tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Day</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Availability</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">From</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">To</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Active Dates*</td>
        <td style="border: 1px solid grey;padding:5px;">'.$actnow.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;">'.$actnow_time.'</td>
        <td style="border: 1px solid grey;padding:5px;">'.$actnow_to.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Monday</td>
        <td style="border: 1px solid grey;padding:5px;">'.$mndy.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;">'.$mndy_time.'</td>
        <td style="border: 1px solid grey;padding:5px;">'.$mndy_to.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Tuesday</td>
        <td style="border: 1px solid grey;padding:5px;">'.$tsdy.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;">'.$tsdy_time.'</td>
        <td style="border: 1px solid grey;padding:5px;">'.$tsdy_to.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Wednesday</td>
        <td style="border: 1px solid grey;padding:5px;">'.$wdnsdy.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;">'.$wdnsdy_time.'</td>
        <td style="border: 1px solid grey;padding:5px;">'.$wdnsdy_to.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Thursday</td>
        <td style="border: 1px solid grey;padding:5px;">'.$thsdy.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;">'.$thsdy_time.'</td>
        <td style="border: 1px solid grey;padding:5px;">'.$thsdy_to.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Friday</td>
        <td style="border: 1px solid grey;padding:5px;">'.$frdy.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;">'.$frdy_time.'</td>
        <td style="border: 1px solid grey;padding:5px;">'.$frdy_to.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Saturday</td>
        <td style="border: 1px solid grey;padding:5px;">'.$stdy.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;">'.$stdy_time.'</td>
        <td style="border: 1px solid grey;padding:5px;">'.$stdy_to.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Sunday</td>
        <td style="border: 1px solid grey;padding:5px;">'.$sndy.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;">'.$sndy_time.'</td>
        <td style="border: 1px solid grey;padding:5px;">'.$sndy_to.'</td>
        </tr>
        <tr>
        <td colspan="1" style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Weekly Remarks</td>
        <td colspan="3" style="border: 1px solid grey;padding:5px;">'.$week_remarks.'</td>
        </tr>
        <tr><td colspan="4" align="center" style="color: #fff;background-color: #337ab7;font-size: 18px;">LANGUAGES DETAILS</td></tr>
        <tr>
        <td colspan="1" style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Speaking Languages</td>
        <td colspan="3" style="border: 1px solid grey;padding:5px;">'.$append_languages.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">UK Citizen?</td>
        <td style="border: 1px solid grey;padding:5px;">'.$uk_citizen.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Permit</td>
        <td style="border: 1px solid grey;padding:5px;">'.$permit.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Driving?</td>
        <td style="border: 1px solid grey;padding:5px;">'.$is_drive.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Recognised Interpreting / Translation Qualification?</td>
        <td style="border: 1px solid grey;padding:5px;">'.$translation_qualifications.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Professional Experience?</td>
        <td style="border: 1px solid grey;padding:5px;">'.$is_experience.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Experience Years</td>
        <td style="border: 1px solid grey;padding:5px;">'.$experience_years.'</td>
        </tr>
        <tr>
        <td colspan="1" style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Areas of specialization</td>
        <td colspan="3" style="border: 1px solid grey;padding:5px;">'.$skills.'</td>
        </tr>
        <tr><td colspan="4" align="center" style="color: #fff;background-color: #337ab7;font-size: 18px;">BANK DETAILS</td></tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Account Name</td>
        <td style="border: 1px solid grey;padding:5px;">'.$acName.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Account Number</td>
        <td style="border: 1px solid grey;padding:5px;">'.$acNo.'</td>
        </tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Bank Name (Branch)</td>
        <td style="border: 1px solid grey;padding:5px;">'.$bnakName.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Sort Code</td>
        <td style="border: 1px solid grey;padding:5px;">'.$acntCode.'</td>
        </tr>';
        if($extra_data->institute){
          $tbl.='<tr><td colspan="4" align="center" style="color: #fff;background-color: #337ab7;font-size: 18px;">EDUCATIONAL DETAILS</td></tr>
          <tr>
          <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Institute Name</td>
          <td style="border: 1px solid grey;padding:5px;">'.$extra_data->institute.'</td>
          <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Qualification</td>
          <td style="border: 1px solid grey;padding:5px;">'.$extra_data->qualification.'</td>
          </tr>
          <tr>
          <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">From Date</td>
          <td style="border: 1px solid grey;padding:5px;">'.$extra_data->from_date.'</td>
          <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">To Date</td>
          <td style="border: 1px solid grey;padding:5px;">'.$extra_data->to_date.'</td>
          </tr>';
        }
        $tbl.=$append_ref.'<tr><td colspan="4" align="center" style="color: #fff;background-color: #337ab7;font-size: 18px;">DISCLAIMER AND SIGNATURE</td></tr>
        <tr>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Signature Name</td>
        <td style="border: 1px solid grey;padding:5px;">'.$name.'</td>
        <td style="border: 1px solid grey;padding:5px;background:gainsboro;font-weight:bold;">Signature Date</td>
        <td style="border: 1px solid grey;padding:5px;">'.$dated.'</td>
        </tr>
        </table>';
        $pdf->writeHTML($tbl, true, false, true, false, '');
        //Close and output PDF document
        $pdfhere = $pdf->Output('', 'I');
