<?php
// set_include_path('/home/customer/www/lsuk.org/public_html/');
include 'db.php';
// include_once ('class.php'); 
class miscClass
{
    public function IsDatedNull($val)
    {
        if ($val == '1001-01-01' || $val == "30-11--0001") {
            return true;
        }

        return false;
    }

    public function dated($val)
    {
        if ($val == '1001-01-01' || $val == "30-11--0001") {
            return 'Not yet fixed!';
        } else {
            return $dated = date_format(date_create($val), 'd-m-Y');
        }
    }
    public function date_time($val)
    {
        if ($val == '1001-01-01 00:00:00' || $val == "30-11--0001 00:00:00") {
            return 'Not yet fixed!';
        } else {
            return $date_time = date_format(date_create($val), 'd-m-Y h:i:s');
        }
    }
    public function ftime($time, $f)
    {
        if (gettype($time) == 'string') {
            $time = strtotime($time);
        }
        return ($f == 24) ? date("hA", $time) : date("h:iA", $time);
    }
    public function sys_date()
    {
      return $dated = date_format(date_create(date("Y-m-d")), 'd-m-Y');
    }
    public function sys_date_db()
    {
      return $dated = date("Y-m-d");
    }
    public function sys_datetime_db()
    {
      return $dated = date("Y-m-d H:i:s");
    }
    public function add_in_date($dat, $dys)
    {
      return date('Y-m-d', strtotime($dat . $dys . ' days'));
    }
    
}
$misc = new miscClass;



//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// include( '../../phpmailer/vendor/autoload.php');
include 'phpmailer/vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$from_name = "LSUK";
$from_add = 'accounts@lsuk.org';
$checks = [];
$mail = new PHPMailer(true);
$query = "SELECT 
* 
FROM
(SELECT 
  interpreter.porder,
  comp_reg.po_req,
  'Interpreter' AS TYPE,
  interpreter.id,
  interpreter.intrpName,
  interpreter.orgName,
  interpreter.inchEmail,
  interpreter_reg.name,
  interpreter.source,
  interpreter.target,
  interpreter.invoic_date,
  interpreter.assignDate,
  interpreter.assignTime,
  interpreter.orgContact,
  interpreter.submited,
  interpreter.aloct_by,
  interpreter.aloct_date,
  interpreter.dated,
  interpreter.hrsubmited,
  interpreter.comp_hrsubmited,
  interpreter.interp_hr_date,
  interpreter.comp_hr_date,
  interpreter.hoursWorkd,
  interpreter.C_hoursWorkd,
  interpreter.total_charges_comp,
  interpreter.cur_vat,
  interpreter.C_otherexpns,
  interpreter.credit_note,
  interpreter.C_admnchargs,
  interpreter.rAmount,
  interpreter.rDate,
  interpreter.sentemail,
  interpreter.printed,
  interpreter.printedby,
  interpreter.deleted_flag,
  interpreter.order_cancel_flag,
  interpreter.nameRef,
  interpreter.orgRef,
  interpreter.invoiceNo,
  interpreter.commit,
  interpreter.disposed_of,
  comp_reg.id AS company_id,
  comp_reg.email,
  comp_reg.abrv AS comp_abrv,
  comp_reg.invEmail 
FROM
  interpreter,
  interpreter_reg,
  comp_reg,
  invoice 
WHERE interpreter.intrpName = interpreter_reg.id 
  AND interpreter.`invoiceNo` = invoice.`invoiceNo` 
AND interpreter.`orgName` NOT LIKE '%AWP%' 
AND interpreter.`orgName` NOT LIKE '%BCC%' 
AND interpreter.`orgName` NOT LIKE '%DPG%' 
AND interpreter.`orgName` NOT LIKE '%Crowly and Co Solicitors%' 
AND interpreter.`orgName` NOT LIKE '%Bristol Hope Service%' 
AND interpreter.`orgName` NOT LIKE '%Resolve West%' 
AND interpreter.`invoic_date`  < DATE_SUB(NOW(),INTERVAL 30 DAY)
  AND interpreter.reminder_sent = 0 
  AND interpreter.orgName = comp_reg.abrv 
  AND interpreter.deleted_flag = 0 
  AND interpreter.disposed_of = 1 
  AND interpreter.order_cancel_flag = 0 
  AND interpreter.commit = 1 
  AND comp_reg.po_req = 1 
  AND interpreter.porder != '' 
  AND (
    ROUND(interpreter.rAmount, 2) < ROUND(
      (
        interpreter.total_charges_comp + (
          interpreter.total_charges_comp * interpreter.cur_vat
        )
      ),
      2
    ) 
    OR interpreter.total_charges_comp = 0
  ) 
UNION
ALL 
SELECT 
  telephone.porder,
  comp_reg.po_req,
  'Telephone' AS TYPE,
  telephone.id,
  telephone.intrpName,
  telephone.orgName,
  telephone.inchEmail,
  interpreter_reg.name,
  telephone.source,
  telephone.target,
  telephone.invoic_date,
  telephone.assignDate,
  telephone.assignTime,
  telephone.orgContact,
  telephone.submited,
  telephone.aloct_by,
  telephone.aloct_date,
  telephone.dated,
  telephone.hrsubmited,
  telephone.comp_hrsubmited,
  telephone.interp_hr_date,
  telephone.comp_hr_date,
  telephone.hoursWorkd,
  telephone.C_hoursWorkd,
  telephone.total_charges_comp,
  telephone.cur_vat,
  0 AS C_otherexpns,
  telephone.credit_note,
  telephone.C_admnchargs,
  telephone.rAmount,
  telephone.rDate,
  telephone.sentemail,
  telephone.printed,
  telephone.printedby,
  telephone.deleted_flag,
  telephone.disposed_of,
  telephone.order_cancel_flag,
  telephone.nameRef,
  telephone.orgRef,
  telephone.invoiceNo,
  telephone.commit,
  comp_reg.id AS company_id,
  comp_reg.email,
  comp_reg.abrv AS comp_abrv,
  comp_reg.invEmail 
FROM
  telephone,
  interpreter_reg,
  comp_reg,
  invoice 
WHERE telephone.intrpName = interpreter_reg.id 
  AND telephone.`invoiceNo` = invoice.`invoiceNo` 
  AND telephone.`orgName` NOT LIKE '%AWP%' 
    AND telephone.`orgName` NOT LIKE '%BCC%' 
    AND telephone.`orgName` NOT LIKE '%DPG%' 
      AND telephone.`orgName` NOT LIKE '%Crowly and Co Solicitors%' 
      AND telephone.`orgName` NOT LIKE '%Bristol Hope Service%' 
      AND telephone.`orgName` NOT LIKE '%Bristol Law Center%'
      AND telephone.`orgName` NOT LIKE '%Resolve West%'
AND telephone.`invoic_date`  < DATE_SUB(NOW(),INTERVAL 30 DAY)
       AND telephone.`porder` != ''
  AND telephone.reminder_sent = 0 
  AND telephone.orgName = comp_reg.abrv 
  AND telephone.deleted_flag = 0 
  AND telephone.disposed_of = 1
  AND telephone.order_cancel_flag = 0 
  AND telephone.commit = 1 
  AND (
    ROUND(telephone.rAmount, 2) < ROUND(
      (
        telephone.total_charges_comp + (
          telephone.total_charges_comp * telephone.cur_vat
        )
      ),
      2
    ) 
    OR telephone.total_charges_comp = 0
  ) 
  AND telephone.invoic_date 
UNION
ALL 
SELECT 
  translation.porder,
  comp_reg.po_req,
  'Translation' AS TYPE,
  translation.id,
  translation.intrpName,
  translation.orgName,
  translation.inchEmail,
  interpreter_reg.name,
  translation.source,
  translation.target,
  translation.invoic_date,
  translation.asignDate AS assignDate,
  '00:00:00' AS assignTime,
  translation.orgContact,
  translation.submited,
  translation.aloct_by,
  translation.aloct_date,
  translation.dated,
  translation.hrsubmited,
  translation.comp_hrsubmited,
  translation.interp_hr_date,
  translation.comp_hr_date,
  translation.numberUnit AS hoursWorkd,
  translation.C_numberUnit AS C_hoursWorkd,
  translation.total_charges_comp,
  translation.cur_vat,
  0 AS C_otherexpns,
  translation.credit_note,
  translation.C_admnchargs,
  translation.rAmount,
  translation.rDate,
  translation.sentemail,
  translation.printed,
  translation.printedby,
  translation.deleted_flag,
  translation.disposed_of,
  translation.order_cancel_flag,
  translation.nameRef,
  translation.orgRef,
  translation.invoiceNo,
  translation.commit,
  comp_reg.id AS company_id,
  comp_reg.email,
  comp_reg.abrv AS comp_abrv,
  comp_reg.invEmail 
FROM
  translation,
  interpreter_reg,
  comp_reg,
  invoice 
WHERE translation.intrpName = interpreter_reg.id 
  AND translation.`invoiceNo` = invoice.`invoiceNo` 
  AND translation.reminder_sent = 0 
  AND translation.orgName = comp_reg.abrv 
  AND translation.deleted_flag = 0 
  AND translation.disposed_of = 1 
  AND translation.order_cancel_flag = 0 
  AND translation.`porder` != ''
  AND translation.commit = 1 
    AND translation.`invoic_date`  < DATE_SUB(NOW(),INTERVAL 30 DAY)
  AND translation.`orgName` NOT LIKE '%AWP%' 
  AND translation.`orgName` NOT LIKE '%BCC%' 
    AND translation.`orgName` NOT LIKE '%DPG%'
    AND translation.`orgName` NOT LIKE '%Crowly and Co Solicitors%'  
    AND translation.`orgName` NOT LIKE '%Bristol Hope Service%'
    AND translation.`orgName` NOT LIKE '%Bristol Law Center%' 
    AND translation.`orgName` NOT LIKE '%Resolve West%'
  AND (
    ROUND(translation.rAmount, 2) < ROUND(
      (
        translation.total_charges_comp + (
          translation.total_charges_comp * translation.cur_vat
        )
      ),
      2
    ) 
    OR translation.total_charges_comp = 0
  ) 
  AND translation.invoic_date) AS grp limit 10";
  $result = mysqli_query($con, $query); 
$i = 0;
// echo $query; die;
// echo "<pre>";var_du($record = mysqli_fetch_assoc($result)); die;
while ($record = mysqli_fetch_assoc($result)) {
    // if($record['company_id'] != 246): // temporaray work
    //     exit($record['company_id']);
    // endif;
    $invoice_id= $record['id'];
    $sendEmailTo = $record['invEmail'] ? $record['invEmail'] : 'aneesgamrani@gmail.com';
    // $sendEmailTo = 'aneesgamrani@gmail.com';
    // echo "<pre>"; print_r($sendEmailTo); exit;
    switch ($record['TYPE']) {
        case 'Interpreter':
            // array_push($checks,$row['id']); continue;
            $table='interpreter';
           
            $query="SELECT interpreter.*,invoice.dated, interpreter_reg.name,comp_reg.name as orgzName,comp_reg.abrv FROM interpreter
            INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
            INNER JOIN interpreter_reg ON interpreter.intrpName=interpreter_reg.id
            INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv

            where multInv_flag=0 AND MONTH(invoice.dated) < MONTH(NOW()) and $table.id=$invoice_id AND $table.reminder_sent = 0
            limit 5";			
            $result = mysqli_query($con,$query);
            if(mysqli_num_rows($result) < 1):
              $updateSql = "UPDATE $table SET reminder_sent = 1 WHERE id = ". $invoice_id;
              $updateResult = mysqli_query($con, $updateSql);
              continue;
            endif;
            while($row = mysqli_fetch_array($result))
            {
            $assignDate=$row['assignDate'];
            $source=$row['source'];
            $buildingName=$row['buildingName'];
            $street=$row['street'];
            $assignCity=$row['assignCity'];
            $nameRef=$row['nameRef'];
            $orgzName=$row['orgzName'];
            $inchCity=$row['inchCity'];
            $inchPcode=$row['inchPcode'];
            $invoiceNo=$row['invoiceNo'];
            $intrpName=$row['name'];
            $inchEmail=$row['inchEmail'];
            $inchRoad=$row['inchRoad'];
            $line1=$row['line1'];
            $line2=$row['line2'];
            $inchNo=$row['inchNo'];
            $hoursWorkd=$row['C_hoursWorkd'];
            $chargInterp=$row['C_chargInterp'];
            $rateHour=$row['C_rateHour'];
            $travelMile=$row['C_travelMile'];
            $rateMile=$row['C_rateMile'];
            $chargeTravel=$row['C_chargeTravel'];
            $travelCost=$row['C_travelCost'];
            $otherCost=$row['C_otherCost'];
            $travelTimeHour=$row['C_travelTimeHour'];
            $travelTimeRate=$row['C_travelTimeRate'];
            $chargeTravelTime=$row['C_chargeTravelTime'];
            $dueDate=$row['dueDate']; $dated=$row['dated'];
            $bookinType=$row['bookinType'];$orgRef=$row['orgRef'];
            $C_admnchargs=$row['C_admnchargs'];
            $C_otherexpns=$row['C_otherexpns'];
            $porder=$row['porder'];
            $C_comments=$row['C_comments'];
            $orgContact=$row['orgContact'];
            $commit=$row['commit'];
            $invoic_date=@$row['invoic_date'];
            $abrv=@$row['abrv'];
            
            }

            $total1=@$rateHour * @$hoursWorkd;$total2=@$travelTimeHour*@$travelTimeRate;$total4=@$rateMile * @$travelMile;$total5=@$total1+@$total2+@$total4+@$C_admnchargs;$vat=@$total5 * 0.2;
            $grand=@$vat+@$total5+@$C_otherexpns;

            $grand=number_format($grand,2);

            // $sub_total = number_format($sub_total,2);

            $total5 = number_format($total5,2);

            $total4 = number_format($total4,2);
            
            $C_otherexpns = number_format($C_otherexpns,2);

            $C_admnchargs = number_format($C_admnchargs,2);

            $vat = number_format($vat,2);
            
            if($grand < 1):
                 $updateSql = "UPDATE $table SET reminder_sent = 1 WHERE id = ". $invoice_id;
                  $updateResult = mysqli_query($con, $updateSql);
                array_push($checks,['invoice_id' => $row,'error'=>' mail not send because amount is 0']);
                break;
            endif;

            // Table with rowspans and THEAD
            $html = <<<EOF
            <!-- EXAMPLE OF CSS STYLE -->
            <style>
                h1 {
                    font-family: times;
                    font-size: 16pt;
                    text-decoration: underline;
                }

                table.first {
                    font-family: helvetica;
                    font-size: 8pt;
                    border-left: 1px solid #ABABAB;
                    border-right: 1px solid #ABABAB;
                    border-top: 1px solid #ABABAB;
                    border-bottom: 1px solid #ABABAB;
                }
                td.first {border: 1px solid #ABABAB;}
                td.second {}
                table..second{}
            </style>
            <br/>
            <h1 class="title" align="center">INVOICE</h1>

            <table width="100%" class="second">
                <tr>
                <td class="second"><strong>Job Address:</strong> {$buildingName}<br/><span style="color:#FFF;"><strong>.......................</strong></span> {$street} {$assignCity}</td>
                <td align="right" class="second">Date: {$misc->dated(@$invoic_date)}</td>
                </tr>
            </table>
            <br/><br/><br/>
            <table width="100%" cellpadding="2" class="second">
            <tr>
                <td class="second"><strong>Invoice No.</strong></td>
                <td class="second">{$invoiceNo}</td>
                <td class="second"><strong>Assignment Date</strong></td>
                <td class="second">{$misc->dated($assignDate)}</td>
            </tr>
            <tr>
                <td  class="second"><strong>Job</strong></td>
                <td  class="second">Interpreting</td>
                <td  class="second"><strong>Job Type</strong></td>
                <td  class="second">Face to Face</td>
            </tr>
            <tr>
                <td  class="second"><strong>Invoice Due Date</strong></td>
                <td  class="second">{$misc->dated(date("Y-m-d", strtotime("+15 days")))}</td> 
                <td  class="second"><strong>Booking Ref / Name</strong></td>
                <td  class="second">{$nameRef}</td>
            </tr>
            <tr>
                <td  class="second"><strong>Purchase Order No.</strong></td>
                <td  class="second">{$porder}</td> 
                <td  class="second"><strong>Linguist</strong></td>
                <td  class="second">{$intrpName}</td>
            </tr>
            <tr>
                <td  class="second"><strong>Language</strong></td>
                <td  class="second">{$source}</td> 
                <td  class="second"><strong>Case Worker Name</strong></td>
                <td  class="second">{$orgContact}</td>
            </tr>
            <tr>
                <td  class="second"><strong>File Ref (Client Ref)</strong></td>
                <td  class="second">{$orgRef}</td>  
                <td  class="second"><strong>Booking Type</strong></td>
                <td  class="second">{$bookinType}</td>
            </tr>
            </table>
            <br/><br/>
            <table width="664" height="282" cellpadding="2" class="first">
            <tr>
            <td width="43" align="center" bgcolor="#D5D5D5"><b>No.</b></td>
            <td width="369" bgcolor="#D5D5D5"><b>Description</b></td>
            <td width="72" align="center" bgcolor="#D5D5D5"> <b>Unit</b></td>
            <td width="77" align="center" bgcolor="#D5D5D5"><b>Unit Cost (&pound;)</b></td>
            <td width="69" align="center" bgcolor="#D5D5D5"><b>Total (&pound;)</b></td>
            </tr>
            
            <tr>
                <td align="center" class="first">1</td>
                <td class="first">Time for Interpreting</td>
                <td align="center" class="first">{$hoursWorkd}</td>
                <td align="center" class="first">{$rateHour}</td>
                <td align="center" class="first">{$total1}</td>
            </tr>
            <tr>
                <td align="center" class="first">2</td>
                <td class="first">Other Expanses</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$C_otherexpns}</td>
            </tr>
            <tr>
                <td align="center" class="first">3</td>
                <td class="first">Travel Time if Applicable</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$travelTimeHour}</td>
            </tr>
            <tr>
                <td align="center" class="first">4</td>
                <td class="first">Travel Cost</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$total2}</td>
            </tr>
            <tr>
                <td align="center" class="first">5</td>
                <td class="first">Milage Cost</td>
                <td align="center" class="first">{$travelMile}</td>
                <td align="center" class="first">{$rateMile}</td>
                <td align="center" class="first">{$total4}</td>
            </tr>
            <tr>
                <td align="center" class="first">7</td>
                <td class="first">Admin Charges</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$C_admnchargs}</td>
            </tr>
            <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
                <td align="right" class="first"><strong>Sub Total</strong></td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first"><strong>{$total5}</strong></td>
            </tr>
            <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
                <td align="right" class="first">Vat @20%</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$vat}</td>
            </tr>
            <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" width="6" height="6" /></td>
                <td align="right" class="first">Non Vat-able Cost</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$C_otherexpns}</td>
            </tr>
            <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
                <td align="right" class="first">Discount</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">0</td>
            </tr>
            <tr>
                <td align="center" class="first">&nbsp;</td>
                <td align="right" class="first"><strong>Total Invoice Cost</strong></td>
                <td align="center" bgcolor="#D5D5D5"><strong>-</strong></td>
                <td align="center" bgcolor="#D5D5D5"><strong>-</strong></td>
                <td align="center" bgcolor="#D5D5D5"><strong>{$grand}</strong></td>
            </tr>
            </table>
            <br/><br/><br/><br/>
            <h5 align="justify">
            Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234. Company Registration Number 7760366 VAT Number 198427362 Thank You For Business With Us</h5>
            <h5>Please pay your invoice within 21 days from the date of invoice.<span style="color:#F00"> Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998"</span> if no payment was made within reasonable time frame</h5>
            EOF;
            
            
            
            $strSubject = "Payment Reminder - Invoice #".$invoiceNo;
            $strMsg = "Dear Client!<br><br>This is to remind you that we have yet to receive payment from yourselves of <strong> &pound;".number_format($grand,2)."</strong> in
                respect of our invoice ".$invoiceNo." which is overdue.<br><br>";
            $strMsg .= $html;
            $strMsg .= "<br><br>We would be grateful if you could let us know when we can expect to receive payment.<br><br>
                Please let us know if you need a PDF copy of the invoice..<br><br>
                Please send us the remittance if this has already been paid.<br><br>
                <span style='color:blue;'>Accounting & Finance Department
                <br>
                Language Services UK Limited
                <br>
                Tel: 01173741967</span><br>
                Email: accounts@lsuk.org
                <br>
                Website: WWW.LSUK.ORG 
                <br>
                Registered in England and Wales 7760366
                <br>
                Address: Suite 3, Davis House, Lodge Causeway Trading Estate, Lodge Causeway, Fishponds, Bristol BS16 3JB
                ";


            
            try {
                $mail->SMTPDebug = 1;
                //$mail->isSMTP();
                //$mailer->Host = 'smtp.office365.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'info@lsuk.org';
                $mail->Password = 'LangServ786';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom($from_add, 'LSUK');
                $mail->addAddress($sendEmailTo);
                $mail->addReplyTo($from_add, 'LSUK');
                $mail->isHTML(true);
                $mail->Subject = $strSubject;
                $mail->Body = $strMsg;

            if($mail->send()){
                // $pdf->Output('', 'I');
                $updateSql = "UPDATE $table SET reminder_sent = 1 WHERE id =  $invoice_id";
                mysqli_query($con,$updateSql);
                
                
            }
            else{
                // echo "Mailer Error: " . $mail->ErrorInfo;
                array_push($checks,['email not send' => $mail->ErrorInfo]);
            }


            } catch (Exception $e) { 
                // echo "Faild! ==> Mailer Error: ".$mail->ErrorInfo;
                array_push($checks,['email' => 'email error not send '.$mail->ErrorInfo]);
            }
            //============================================================+
            // END OF FILE
            //============================================================+

            // $pdf->Output('', 'I');
            break;
        case 'Telephone':
        //    array_push($checks,$row['id']); break;
            $table='telephone';
            $query="SELECT $table.*,invoice.dated, interpreter_reg.name,comp_reg.name as orgzName,comp_reg.abrv FROM $table
            INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
            INNER JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
            INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv

             where multInv_flag=0 AND MONTH(invoice.dated) < MONTH(NOW()) and $table.id=$invoice_id AND $table.reminder_sent = 0
            limit 5";		
            $result = mysqli_query($con,$query);
            // var_dump(mysqli_num_rows($result) < 1);
            if(mysqli_num_rows($result) < 1):
              $updateSql = "UPDATE $table SET reminder_sent = 1 WHERE id = ". $invoice_id;
              $updateResult = mysqli_query($con, $updateSql);
              break;
            endif;
            while($row = mysqli_fetch_array($result)){$assignDate=$row['assignDate'];$source=$row['source'];$orgzName=$row['orgzName'];$assignCity=$row['assignCity'];$street=$row['street'];$inchCity=$row['inchCity'];$intrpName=$row['name'];$buildingName=$row['buildingName'];$inchRoad=$row['inchRoad'];$invoiceNo=$row['invoiceNo'];$line1=$row['line1'];$line2=$row['line2'];$inchNo=$row['inchNo'];$nameRef=$row['nameRef'];$inchEmail=$row['inchEmail'];$inchRoad=$row['inchRoad'];$hoursWorkd=$row['C_hoursWorkd'];$calCharges=$row['calCharges'];$C_otherCharges=$row['C_otherCharges'];$chargInterp=$row['C_chargInterp'];$rateHour=$row['C_rateHour'];$dueDate=$row['dueDate'];$dated=date_format(date_create($row['dated']), 'd-m-Y');$line1=$row['line1'];$inchNo=$row['inchNo'];$inchPcode=$row['inchPcode'];$bookinType=$row['bookinType'];$orgRef=$row['orgRef'];$C_admnchargs=$row['C_admnchargs'];$porder=$row['porder'];$C_comments=$row['C_comments'];$orgContact=$row['orgContact'];$commit=$row['commit']; $invoic_date=@$row['invoic_date'];$abrv=@$row['abrv'];}
            
            $total1=$rateHour * $hoursWorkd;$sub_total=$calCharges + $C_otherCharges + ($rateHour * $hoursWorkd)+$C_admnchargs;$vat=$sub_total * .2;$grand=$sub_total + $vat;
            
                $grand=number_format($grand,2);

                if($grand < 1):
                 $updateSql = "UPDATE $table SET reminder_sent = 1 WHERE id = ". $invoice_id;
                  $updateResult = mysqli_query($con, $updateSql);
                array_push($checks,['invoice_id' => $row,'error'=>' mail not send because amount is 0']);
                break;
            endif;
           
            $total1 = number_format($total1,2);
            
            $C_admnchargs = number_format($C_admnchargs,2);

            $vat = number_format($vat,2);

           
            
            // Table with rowspans and THEAD
            $html = <<<EOF
            <!-- EXAMPLE OF CSS STYLE -->
            <style>
                h1 {
                    font-family: times;
                    font-size: 16pt;
                    text-decoration: underline;
                }
            
                table.first {
                    font-family: helvetica;
                    font-size: 8pt;
                    border-left: 1px solid #ABABAB;
                    border-right: 1px solid #ABABAB;
                    border-top: 1px solid #ABABAB;
                    border-bottom: 1px solid #ABABAB;
                }
                td.first {border: 1px solid #ABABAB;}
                td.second {}
                table..second{}
            </style>
            <br/>
            <h1 class="title" align="center">INVOICE</h1>
            
              <table width="100%" class="second">
                <tr>
                  <td class="second"><strong>Job Address:</strong> {$inchNo}<br/><span style="color:#FFF;"><strong>.......................</strong></span> {$line1} {$line2} {$inchRoad} {$inchCity}</td>
                  <td align="right" class="second">Date: {$misc->dated(@$invoic_date)}</td>
                </tr>
              </table>
            <br/><br/><br/>
            <table width="100%" cellpadding="2" class="second">
              <tr>
                <td class="second"><strong>Invoice No.</strong></td>
                <td class="second">{$invoiceNo}</td>
                <td class="second"><strong>Assignment Date</strong></td>
                <td class="second">{$misc->dated($assignDate)}</td>
              </tr>
              <tr>
                <td  class="second"><strong>Job</strong></td>
                <td  class="second">Interpreting</td>
                <td  class="second"><strong>Job Type</strong></td>
                <td  class="second">Telephone</td>
              </tr>
              <tr>
                <td  class="second"><strong>Invoice Due Date</strong></td>
                <td  class="second">{$misc->dated(date("Y-m-d", strtotime("+15 days")))}</td> 
                <td  class="second"><strong>Booking Ref / Name</strong></td>
                <td  class="second">{$nameRef}</td>
              </tr>
              <tr>
                <td  class="second"><strong>Purchase Order No.</strong></td>
                <td  class="second">{$porder}</td> 
                <td  class="second"><strong>Linguist</strong></td>
                <td  class="second">{$intrpName}</td>
              </tr>
              <tr>
                <td  class="second"><strong>Language</strong></td>
                <td  class="second">{$source}</td> 
                <td  class="second"><strong>Case Worker Name</strong></td>
                <td  class="second">{$orgContact}</td>
              </tr>
              <tr>
                <td  class="second"><strong>File Ref (Client Ref)</strong></td>
                <td  class="second">{$orgRef}</td>  
                <td  class="second"><strong>Booking Type</strong></td>
                <td  class="second">{$bookinType}</td>
              </tr>
            </table>
            <br/><br/>
            <table width="664" height="282" cellpadding="2" class="first">
              <tr>
              <td width="43" align="center" bgcolor="#D5D5D5"><b>No.</b></td>
              <td width="369" bgcolor="#D5D5D5"><b>Description</b></td>
              <td width="72" align="center" bgcolor="#D5D5D5"> <b>Unit</b></td>
              <td width="77" align="center" bgcolor="#D5D5D5"><b>Unit Cost (&pound;)</b></td>
              <td width="69" align="center" bgcolor="#D5D5D5"><b>Total (&pound;)</b></td>
             </tr>
             
              <tr>
                <td align="center" class="first">1</td>
                <td class="first">Time for Interpreting</td>
                <td align="center" class="first">{$hoursWorkd}</td>
                <td align="center" class="first">{$rateHour}</td>
                <td align="center" class="first">{$total1}</td>
              </tr>
              <tr>
                <td align="center" class="first">2</td>
                <td class="first">Other Expanses</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$C_otherCharges}</td>
              </tr>
              <tr>
                <td align="center" class="first">3</td>
                <td class="first">Call Length Cost</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$calCharges}</td>
              </tr>
              <tr>
                <td align="center" class="first">7</td>
                <td class="first">Admin Charges</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$C_admnchargs}</td>
              </tr>
              <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
                <td align="right" class="first"><strong>Sub Total</strong></td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first"><strong>{$sub_total}</strong></td>
              </tr>
              <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
                <td align="right" class="first">Vat @20%</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$vat}</td>
              </tr>
              <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" width="6" height="6" /></td>
                <td align="right" class="first">Non Vat-able Cost</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$C_otherCharges}</td>
              </tr>
              <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
                <td align="right" class="first">Discount</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">0</td>
              </tr>
              <tr>
                <td align="center" class="first">&nbsp;</td>
                <td align="right" class="first"><strong>Total Invoice Cost</strong></td>
                <td align="center" bgcolor="#D5D5D5"><strong>-</strong></td>
                <td align="center" bgcolor="#D5D5D5"><strong>-</strong></td>
                <td align="center" bgcolor="#D5D5D5"><strong>{$grand}</strong></td>
              </tr>
            </table>
            <br/><br/><br/><br/>
            <h5 align="justify">
            Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234. Company Registration Number 7760366 VAT Number 198427362 Thank You For Business With Us</h5>
            <h5>Please pay your invoice within 21 days from the date of invoice.<span style="color:#F00"> Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998"</span> if no payment was made within reasonable time frame</h5>
            EOF;
            
            $strSubject = "Payment Reminder - Invoice #".$invoiceNo;
            $strMsg = "Dear Client!<br><br>This is to remind you that we have yet to receive payment from yourselves of <strong> &pound;".number_format($grand,2)."</strong> in
              respect of our invoice ".$invoiceNo." which is overdue.<br><br>";
            $strMsg .= $html;
            $strMsg .="<br><br>We would be grateful if you could let us know when we can expect to receive payment.<br><br>
              Please let us know if you need a PDF copy of the invoice..<br><br>
              Please send us the remittance if this has already been paid.<br><br>
              <span style='color:blue;'>Accounting & Finance Department
              <br>
              Language Services UK Limited
              <br>
              Tel: 01173741967</span><br>
              Email: accounts@lsuk.org
              <br>
              Website: WWW.LSUK.ORG 
              <br>
              Registered in England and Wales 7760366
              <br>
              Address: Suite 3, Davis House, Lodge Causeway Trading Estate, Lodge Causeway, Fishponds, Bristol BS16 3JB
              <br><br><small>If you have received this email by mistake, please notify the sender by reply-email and delete this message instantly.</small>";

            
            try {
                $mail->SMTPDebug = 1;
                //$mail->isSMTP();
                //$mailer->Host = 'smtp.office365.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'info@lsuk.org';
                $mail->Password = 'LangServ786';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom($from_add, 'LSUK');
                $mail->addAddress($sendEmailTo);
                $mail->addReplyTo($from_add, 'LSUK');
                $mail->isHTML(true);
                $mail->Subject = $strSubject;
                $mail->Body = $strMsg;

            if($mail->send()){

                $updateSql = "UPDATE $table SET reminder_sent = 1 WHERE id =  $invoice_id";
                mysqli_query($con,$updateSql);
                
            }
            else{
                // echo "Mailer Error: " . $mail->ErrorInfo;
                array_push($checks,['email not send' => $mail->ErrorInfo]);
            }


            } catch (Exception $e) { 
                // echo "Faild! ==> Mailer Error: ".$mail->ErrorInfo;
                array_push($checks,['email' => 'email error not send '.$mail->ErrorInfo]);
            }
            
            //============================================================+
            // END OF FILE
            //============================================================+
            
            
            break;
        case 'Translation':
            // array_push($checks,$row['id']); break;
            $table='translation';
            $query="SELECT $table.*,invoice.dated, interpreter_reg.name as intrpName,comp_reg.*,comp_reg.name as orgzName,comp_reg.abrv FROM $table
            INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
            INNER JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
            INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv

            where multInv_flag=0 AND MONTH(invoice.dated) < MONTH(NOW()) and $table.id=$invoice_id AND $table.reminder_sent = 0
            limit 5";			
            $result = mysqli_query($con,$query);
            if(mysqli_num_rows($result) < 1):
              $updateSql = "UPDATE $table SET reminder_sent = 1 WHERE id = ". $invoice_id;
              $updateResult = mysqli_query($con, $updateSql);
              break;
            endif;
            while($row = mysqli_fetch_array($result)){$orgzName=$row['orgzName'];$buildingName=$row['buildingName'];$line1=$row['line1'];$line2=$row['line2'];$city=$row['city'];$postCode=$row['postCode'];$source=$row['source'];$asignDate=$row['asignDate'];$orgName=$row['orgName'];$buildingName=$row['buildingName'];$orgContact=$row['orgContact'];$invoiceNo=$row['invoiceNo'];$inchEmail=$row['inchEmail'];$C_numberUnit=$row['C_numberUnit'];$C_rpU=$row['C_rpU'];$C_otherCharg=$row['C_otherCharg'];$total_charges_comp=$row['total_charges_comp'];$dueDate=$row['dueDate'];$dated=$row['dated'];$nameRef=$row['nameRef'];$transType=$row['transType'];$docType=$row['docType'];$trans_detail=$row['trans_detail']; $intrpName=$row['intrpName'];$bookinType=$row['bookinType'];$certificationCost=$row['certificationCost'];$proofCost=$row['proofCost'];$postageCost=$row['postageCost'];$C_numberWord=$row['C_numberWord'];$C_rpW=$row['C_rpW'];$C_admnchargs=$row['C_admnchargs'];$porder=$row['porder'];$C_comments=$row['C_comments'];$orgRef=$row['orgRef'];$commit=$row['commit']; $invoic_date=@$row['invoic_date'];$abrv=@$row['abrv'];}
            if($porder==''){$porder='Nil';}
            $unitCost=$C_numberUnit * $C_rpU;$wordCost=$C_numberWord * $C_rpW;$total=$unitCost + $wordCost + $certificationCost + $postageCost + $proofCost + $C_otherCharg+$C_admnchargs;$vat=$total * 0.2;$grand=$vat + $total;
            $grand=number_format($grand,2);
            $unitCost = number_format($unitCost,2);
            $total = number_format($total,2);

            // $total5 = number_format($total5,2);

            // $total4 = number_format($total,2);
            
            $C_otherCharg = number_format($C_otherCharg,2);

            $C_admnchargs = number_format($C_admnchargs,2);

            $vat = number_format($vat,2);


            if($grand < 1):
                 $updateSql = "UPDATE $table SET reminder_sent = 1 WHERE id = ". $invoice_id;
                  $updateResult = mysqli_query($con, $updateSql);
                array_push($checks,['invoice_id' => $row,'error'=>' mail not send because amount is 0']);
                continue;
            endif;
            // Table with rowspans and THEAD
            $html = <<<EOF
            <!-- EXAMPLE OF CSS STYLE -->
            <style>
                h1 {
                    font-family: times;
                    font-size: 16pt;
                    text-decoration: underline;
                }

                table.first {
                    font-family: helvetica;
                    font-size: 8pt;
                    border-left: 1px solid #ABABAB;
                    border-right: 1px solid #ABABAB;
                    border-top: 1px solid #ABABAB;
                    border-bottom: 1px solid #ABABAB;
                }
                td.first {border: 1px solid #ABABAB;}
                td.second {}
                table..second{}
            </style>
            <br/>
            <h1 class="title" align="center">INVOICE</h1>

            <table width="100%" class="second">
                <tr>
                <td class="second"><strong>Job Address:</strong> {$orgzName}<br/><span style="color:#FFF;"><strong>.......................</strong></span> {$line1} {$line2} {$city} {$postCode}</td>
                <td align="right" class="second">Date: {$misc->dated(@$invoic_date)}</td>
                </tr>
            </table>
            <br/><br/><br/>
            <table width="100%" cellpadding="2" class="second">
            <tr>
                <td class="second"><strong>Invoice No.</strong></td>
                <td class="second">{$invoiceNo}</td>
                <td class="second"><strong>Assignment Date</strong></td>
                <td class="second">{$misc->dated($asignDate)}</td>
            </tr>
            <tr>
                <td  class="second"><strong>Job</strong></td>
                <td  class="second">Interpreting</td>
                <td  class="second"><strong>Job Type</strong></td>
                <td  class="second">Translation</td>
            </tr>
            <tr>
                <td  class="second"><strong>Invoice Due Date</strong></td>
                <td  class="second">{$misc->dated(date("Y-m-d", strtotime("+15 days")))}</td> 
                <td  class="second"><strong>Booking Ref / Name</strong></td>
                <td  class="second">{$nameRef}</td>
            </tr>
            <tr>
                <td  class="second"><strong>Purchase Order No.</strong></td>
                <td  class="second">{$porder}</td> 
                <td  class="second"><strong>Linguist</strong></td>
                <td  class="second">{$intrpName}</td>
            </tr>
            <tr>
                <td  class="second"><strong>Language</strong></td>
                <td  class="second">{$source}</td> 
                <td  class="second"><strong>Case Worker Name</strong></td>
                <td  class="second">{$orgContact}</td>
            </tr>
            <tr>
                <td  class="second"><strong>File Ref (Client Ref)</strong></td>
                <td  class="second">{$orgRef}</td>  
                <td  class="second"><strong>Booking Type</strong></td>
                <td  class="second">{$bookinType}</td>
            </tr>
            </table>
            <br/><br/>
            <table width="664" height="282" cellpadding="2" class="first">
            <tr>
            <td width="43" align="center" bgcolor="#D5D5D5"><b>No.</b></td>
            <td width="369" bgcolor="#D5D5D5"><b>Description</b></td>
            <td width="72" align="center" bgcolor="#D5D5D5"> <b>Unit</b></td>
            <td width="77" align="center" bgcolor="#D5D5D5"><b>Unit Cost (&pound;)</b></td>
            <td width="69" align="center" bgcolor="#D5D5D5"><b>Total (&pound;)</b></td>
            </tr>
            
            <tr>
                <td align="center" class="first">1</td>
                <td class="first">Minimum Translation  Cost (units)</td>
                <td align="center" class="first">{$C_numberUnit}</td>
                <td align="center" class="first">{$C_rpU}</td>
                <td align="center" class="first">{$unitCost}</td>
            </tr>
            <tr>
                <td align="center" class="first">2</td>
                <td class="first">Cost Per Word</td>
                <td align="center" class="first">{$C_numberWord}</td>
                <td align="center" class="first">{$C_rpW}</td>
                <td align="center" class="first">{$wordCost}</td>
            </tr>
            <tr>
                <td align="center" class="first">3</td>
                <td class="first">Certification Cost</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$certificationCost}</td>
            </tr>
            <tr>
                <td align="center" class="first">3</td>
                <td class="first">Proof reading Cost</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$proofCost}</td>
            </tr>
            <tr>
                <td align="center" class="first">3</td>
                <td class="first">Postage Cost</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$postageCost}</td>
            </tr>
            <tr>
                <td align="center" class="first">3</td>
                <td class="first">Other Expenses</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$C_otherCharg}</td>
            </tr>
            <tr>
                <td align="center" class="first">7</td>
                <td class="first">Admin Charges</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$C_admnchargs}</td>
            </tr>
            <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
                <td align="right" class="first"><strong>Sub Total</strong></td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first"><strong>{$total}</strong></td>
            </tr>
            <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
                <td align="right" class="first">Vat @20%</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">{$vat}</td>
            </tr>
            <tr>
                <td align="center" class="first"><img src="images/bullet-tick.gif" alt="" width="6" height="6" /></td>
                <td align="right" class="first">Discount</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">-</td>
                <td align="center" class="first">0</td>
            </tr>
            <tr>
                <td align="center" class="first">&nbsp;</td>
                <td align="right" class="first"><strong>Total Invoice Cost</strong></td>
                <td align="center" bgcolor="#D5D5D5"><strong>-</strong></td>
                <td align="center" bgcolor="#D5D5D5"><strong>-</strong></td>
                <td align="center" bgcolor="#D5D5D5"><strong>{$grand}</strong></td>
            </tr>
            </table>
            <br/><br/><br/><br/>
            <h5 align="justify">
            Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234. Company Registration Number 7760366 VAT Number 198427362 Thank You For Business With Us</h5>
            <h5>Please pay your invoice within 21 days from the date of invoice.<span style="color:#F00"> Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998"</span> if no payment was made within reasonable time frame</h5>
            EOF;
           
            
            $strSubject = "Payment Reminder - Invoice #".$invoiceNo;
            $strMsg = "Dear Client!<br><br>This is to remind you that we have yet to receive payment from yourselves of <strong> &pound;".number_format($grand,2)."</strong> in
              respect of our invoice ".$invoiceNo." which is overdue.<br><br>
              We would be grateful if you could let us know when we can expect to receive payment.<br><br>";
            $strMsg .= $html ;
            $strMsg .= "Please let us know if you need a PDF copy of the invoice.<br><br>
              Please send us the remittance if this has already been paid.<br><br>
              <span style='color:blue;'>Accounting & Finance Department
              <br>
              Language Services UK Limited
              <br>
              Tel: 01173741967</span><br>
              Email: accounts@lsuk.org
              <br>
              Website: WWW.LSUK.ORG 
              <br>
              Registered in England and Wales 7760366
              <br>
              Address: Suite 3, Davis House, Lodge Causeway Trading Estate, Lodge Causeway, Fishponds, Bristol BS16 3JB
              <br><br><small>If you have received this email by mistake, please notify the sender by reply-email and delete this message instantly.</small>";


            
            try {
                $mail->SMTPDebug = 1;
                //$mail->isSMTP();
                //$mailer->Host = 'smtp.office365.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'info@lsuk.org';
                $mail->Password = 'LangServ786';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom($from_add, 'LSUK');
                $mail->addAddress($sendEmailTo);
                $mail->addReplyTo($from_add, 'LSUK');
                $mail->isHTML(true);
                $mail->Subject = $strSubject;
                $mail->Body = $strMsg;

              if($mail->send()){
                  // $pdf->Output('', 'I');
                  $updateSql = "UPDATE $table SET reminder_sent = 1 WHERE id = ". $invoice_id;
                  $updateResult = mysqli_query($con, $updateSql);
                  
              }
              else{
                //   echo "Mailer Error: " . $mail->ErrorInfo;
                array_push($checks,['email not send' => $mail->ErrorInfo]);
              }


            } catch (Exception $e) { 
                // echo "Faild! ==> Mailer Error: ".$mail->ErrorInfo;
                array_push($checks,['email' => 'email error not send '.$mail->ErrorInfo]);
            }

            //============================================================+
            // END OF FILE
            //============================================================+

            
            break;
        
        }
        // array_push($checks,['i' => $i++]);
       
    }

    // echo "<pre>";print_r($checks);





