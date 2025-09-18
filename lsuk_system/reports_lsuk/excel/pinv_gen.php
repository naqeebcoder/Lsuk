<?php
session_start();
include 'db.php';include_once ('class.php'); 
require '../../../vendor/autoload.php'; // Include the PhpSpreadsheet library

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$p_org = @$_GET['p_org'];

if(isset($_GET['p_org'])){
    $p_org = $_GET['p_org'];
    $p_org_q = $acttObj->read_specific("GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries", " parent_comp=$p_org")['ch_ids']?:'0';
    $p_org_ad = ($p_org_q!=0?" and comp_reg.id IN ($p_org_q) ":" and comp_reg.id IN ($p_org) ");
    $get_pname = $acttObj->read_specific("name,abrv,po_req","comp_reg"," id=$p_org ");
    $p_name = $get_pname['name'];
    $p_abrv = $get_pname['abrv'];
    $po_req = $get_pname['po_req'];

}else{
    $p_org_ad = $p_org;
    die();exit();
}

$query =
    'SELECT * from (SELECT interpreter.porder,comp_reg.po_req,"Interpreter" as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.invoic_date,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.name as comp_name,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1  and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) '.$p_org_ad.'
    UNION ALL SELECT telephone.porder,comp_reg.po_req,"Telephone" as type,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,telephone.source,telephone.invoic_date,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.name as comp_name,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) '.$p_org_ad.'
    UNION ALL SELECT translation.porder,comp_reg.po_req,"Translation" as type,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,translation.source,translation.invoic_date,translation.asignDate as assignDate,"00:00:00" as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.name as comp_name,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 AND translation.disposed_of = 0 and translation.order_cancel_flag=0 and translation.commit=1 and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) '.$p_org_ad.') as grp ORDER BY comp_name,assignDate ASC';
                     
// echo $query;exit;
$result = mysqli_query($con, $query);
$styles = [
    "fill" => [
        "fillType" => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        "startColor" => [ "rgb" => "163198" ]
    ],
    "font" => [
        "color" => [ "rgb" => "FFFFFF" ]
    ]
];
$styles2 = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ],
    ],
];
$headStyle = [
    'font' => [
        'bold' => true,
        'size'  => 28,
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    ],
];
// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$html = new PhpOffice\PhpSpreadsheet\Helper\Html();
// $HTMLCODE = $html->toRichTextObject($CODE);

// Set the active sheet
$sheet = $spreadsheet->getActiveSheet();
$idp=1;
while($idp<5){
    if($po_req==1){
        foreach (range('A', 'L') as $letra) {            
            $sheet->mergeCells('A'.$idp.':L'.$idp);
        }
    }else{
        foreach (range('A', 'K') as $letra) {            
            $sheet->mergeCells('A'.$idp.':K'.$idp);
        }
    }
    $idp++;
}

$rp_date = date('Y-m-d');
$sheet->setCellValue('A1', $p_name.' Pending Invoices');
$sheet->setCellValue('A3', 'Report Date: '.$rp_date);

$sheet->getStyle('A1')->applyFromArray($headStyle);

// Add some data to the sheet
$sheet->setCellValue('A5', 'Sr.No');
$sheet->setCellValue('B5', 'Type');
$sheet->setCellValue('C5', 'Assignment Date');
$sheet->setCellValue('D5', 'Invoice Number');
$sheet->setCellValue('E5', 'Contact Name');
$sheet->setCellValue('F5', 'Client Reference');
$sheet->setCellValue('G5', 'Linguist');
if($po_req==1){
    $sheet->setCellValue('H5', 'Purchase order #');
    $sheet->setCellValue('I5', 'Net Amount');
    $sheet->setCellValue('J5', 'VAT');
    $sheet->setCellValue('K5', 'NON VAT');
    $sheet->setCellValue('L5', 'Total Amount');
}else{
    $sheet->setCellValue('H5', 'Net Amount');
    $sheet->setCellValue('I5', 'VAT');
    $sheet->setCellValue('J5', 'NON VAT');
    $sheet->setCellValue('K5', 'Total Amount');
}
$sheet->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
$sheet->getStyle('F')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);


// $sheet->setCellValue('M1', 'Company Name');


// Set the header row bold
if($po_req==1){
    $sheet->getStyle('A5:L5')->getFont()->setBold(true);
    $sheet->getStyle("A5:L5")->applyFromArray($styles);
    foreach (range('B', 'L') as $letra) {            
        $sheet->getColumnDimension($letra)->setAutoSize(false);
        $sheet->getColumnDimension($letra)->setWidth('15');
        
    }
}else{
    $sheet->getStyle('A5:K5')->getFont()->setBold(true);
    $sheet->getStyle("A5:K5")->applyFromArray($styles);
    foreach (range('B', 'K') as $letra) {            
        $sheet->getColumnDimension($letra)->setAutoSize(false);
        $sheet->getColumnDimension($letra)->setWidth('15');
        
    }
}
// $sheet->getColumnDimension('B:I')->setAutoSize(false);
// $sheet->getColumnDimension('B')->setWidth('30');
// $sheet->getColumnDimension('C:I')->setWidth('30');

$i=1;
$count=6;
$cur_comp = "";
$new_comp = "";
$tot_amount = 0;
$comp_val_arr = $comp_all_arr = $comp_tot_arr = array();
while($row = mysqli_fetch_object($result)){
    $append_invoiceNo='';
        if(!empty($row->credit_note) && $row->type=="Interpreter"){
          $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$row->id." and order_type='f2f'")['counter'];
        }elseif(!empty($row->credit_note) && $row->type=="Telephone"){
            $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$row->id." and order_type='tp'")['counter'];
        }elseif(!empty($row->credit_note) && $row->type=="Translation"){
            $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$row->id." and order_type='tr'")['counter'];
        }
        $table_po='';
        if($row->po_req==1 && $row->porder!=''){ $table_po= $row->porder;}else if($row->po_req==1 && $row->porder==''){$table_po= '<b style="color:red;">Missing!</b>';}else{$table_po= '<b>Not required!</b>';}
    
        if($row->type=='Interpreter'){
            $totalforvat = $row->total_charges_comp;
            $vatpay = $totalforvat * $row->cur_vat;
            $totinvnow = $totalforvat + $vatpay + $row->C_otherexpns;
        }else if($row->type=='Telephone'){
            $totalforvat=$row->total_charges_comp;
            $vatpay=$totalforvat*$row->cur_vat;
            $totinvnow=$totalforvat+$vatpay;
        }else{
            $totalforvat=$row->total_charges_comp;
            $vatpay=$totalforvat*$row->cur_vat;
            $totinvnow=$totalforvat+$vatpay;
        }
    if($totinvnow<=0){ continue; }
    
    $new_comp = $row->comp_name;
    if($cur_comp!=$new_comp){
        UpdateAllTotals($count,$comp_val_arr,$comp_all_arr,$comp_tot_arr,$sheet,$po_req);
        $sheet->setCellValue("A" . $count, $new_comp);
        $sheet->getStyle("A" . $count)->getAlignment()->setWrapText(true); 
        $sheet->getStyle("A" . $count)->applyFromArray($styles);
        $sheet->getColumnDimension("A")->setAutoSize(false);
        $sheet->getColumnDimension("A")->setWidth('30');
        $cur_comp=$new_comp;
        $count++;
    }
    $tot_amount = $tot_amount+$misc->numberFormat_fun($totinvnow);
    $sheet->setCellValue("A" . $count, $i);
    $sheet->setCellValue("B" . $count, ($row->type=="Interpreter"?"Face 2 Face":$row->type));
    $sheet->setCellValue("C" . $count, $row->assignDate);
    $sheet->setCellValue("D" . $count, $row->invoiceNo.$append_invoiceNo);
    $sheet->setCellValue("E" . $count, $row->orgContact);
    $sheet->setCellValue("F" . $count, $row->orgRef);
    $sheet->setCellValue("G" . $count, $row->name);
    if($po_req==1){
        $sheet->setCellValue("H" . $count, $html->toRichTextObject($table_po));
        $sheet->setCellValue("I" . $count, $row->total_charges_comp);
        $sheet->setCellValue("J" . $count, $row->total_charges_comp*$row->cur_vat);
        $sheet->setCellValue("K" . $count, $row->C_otherexpns);
        $sheet->setCellValue("L" . $count, $misc->numberFormat_fun($totinvnow));
    }else{
        $sheet->setCellValue("H" . $count, $row->total_charges_comp);
        $sheet->setCellValue("I" . $count, $row->total_charges_comp*$row->cur_vat);
        $sheet->setCellValue("J" . $count, $row->C_otherexpns);
        $sheet->setCellValue("K" . $count, $misc->numberFormat_fun($totinvnow));
    }
    fillArr($count,$comp_val_arr,$po_req);
    
    // array_push($comp_val_arr,"L". $count);
    // $sheet->setCellValue("M" . $count, $row->comp_name.'('.$row->orgName.')');
    // $sheet->getColumnDimension("A")->setWidth('10');
    $count++;
    $i++;
}
UpdateAllTotals($count,$comp_val_arr,$comp_all_arr,$comp_tot_arr,$sheet,$po_req);
$sheet->setCellValue('A'.$count, '');
$sheet->setCellValue('B'.$count, '');
$sheet->setCellValue('C'.$count, '');
$sheet->setCellValue('D'.$count, '');
$sheet->setCellValue('E'.$count, '');
$sheet->setCellValue('F'.$count, '');
if($po_req==1){
    $sheet->setCellValue('G'.$count, '');
    $sheet->setCellValue('H'.$count, 'TOTAL');
    $sheet->setCellValue('I'.$count, "=SUM(".$comp_tot_arr['net'].")");
    $sheet->setCellValue('J'.$count, "=SUM(".$comp_tot_arr['vat'].")");
    $sheet->setCellValue('K'.$count, "=SUM(".$comp_tot_arr['nonvat'].")");
    $sheet->setCellValue('L'.$count, "=SUM(".$comp_tot_arr['total'].")");
}else{
    $sheet->setCellValue('G'.$count, 'TOTAL');
    $sheet->setCellValue('H'.$count, "=SUM(".$comp_tot_arr['net'].")");
    $sheet->setCellValue('I'.$count, "=SUM(".$comp_tot_arr['vat'].")");
    $sheet->setCellValue('J'.$count, "=SUM(".$comp_tot_arr['nonvat'].")");
    $sheet->setCellValue('K'.$count, "=SUM(".$comp_tot_arr['total'].")");
}

$sheet->getStyle('A'.$count.':L'.$count)->getFont()->setBold(true);
$sheet->getStyle('E5:E'.$sheet->getHighestRow())->getAlignment()->setWrapText(true); 
$sheet->getStyle('F5:F'.$sheet->getHighestRow())->getAlignment()->setWrapText(true); 
$sheet->getStyle('G5:G'.$sheet->getHighestRow())->getAlignment()->setWrapText(true); 
if($po_req==1){
    $sheet->getStyle("A5:L".$sheet->getHighestRow())->applyFromArray($styles2);
}else{
    $sheet->getStyle("A5:K".$sheet->getHighestRow())->applyFromArray($styles2);
}

// Create a writer object
$writer = new Xlsx($spreadsheet);

// Set the output file name and path
$filename = "/home/customer/www/lsuk.org/public_html/lsuk_system/file_folder/pinv_list/".$p_abrv."_pending_invoices.xlsx";
// Save the spreadsheet to a file
$writer->save($filename);

echo "Spreadsheet created successfully.";

function fillArr($count,&$arr_vals,$po_req){
    if($po_req==1){
        if(empty($arr_vals['net'])){
            $arr_vals['net'].="I". $count;
            $arr_vals['vat'].="J". $count;
            $arr_vals['nonvat'].="K". $count;
            $arr_vals['total'].="L". $count;
        }else{
            $arr_vals['net'].="+I". $count;
            $arr_vals['vat'].="+J". $count;
            $arr_vals['nonvat'].="+K". $count;
            $arr_vals['total'].="+L". $count;
        }
    }else{
        if(empty($arr_vals['net'])){
            $arr_vals['net'].="H". $count;
            $arr_vals['vat'].="I". $count;
            $arr_vals['nonvat'].="J". $count;
            $arr_vals['total'].="K". $count;
        }else{
            $arr_vals['net'].="+H". $count;
            $arr_vals['vat'].="+I". $count;
            $arr_vals['nonvat'].="+J". $count;
            $arr_vals['total'].="+K". $count;
        }
    }
    
}

function UpdateAllTotals(&$count,&$comp_val_arr,&$comp_all_arr,&$comp_tot_arr,&$sheet,$po_req){
    if($count!=6){
        $sheet->setCellValue('A'.$count, '');
        $sheet->setCellValue('B'.$count, '');
        $sheet->setCellValue('C'.$count, '');
        $sheet->setCellValue('D'.$count, '');
        $sheet->setCellValue('E'.$count, '');
        $sheet->setCellValue('F'.$count, '');
        if($po_req==1){
            $sheet->setCellValue('G'.$count, '');
            $sheet->setCellValue('H'.$count, 'SUB TOTAL');
            $sheet->setCellValue('I'.$count, "=SUM(".$comp_val_arr['net'].")");
            $sheet->setCellValue('J'.$count, "=SUM(".$comp_val_arr['vat'].")");
            $sheet->setCellValue('K'.$count, "=SUM(".$comp_val_arr['nonvat'].")");
            $sheet->setCellValue('L'.$count, "=SUM(".$comp_val_arr['total'].")");
        }else{
            $sheet->setCellValue('G'.$count, 'SUB TOTAL');
            $sheet->setCellValue('H'.$count, "=SUM(".$comp_val_arr['net'].")");
            $sheet->setCellValue('I'.$count, "=SUM(".$comp_val_arr['vat'].")");
            $sheet->setCellValue('J'.$count, "=SUM(".$comp_val_arr['nonvat'].")");
            $sheet->setCellValue('K'.$count, "=SUM(".$comp_val_arr['total'].")");
        }
        
        $sheet->getStyle('A'.$count.':L'.$count)->getFont()->setBold(true);

        fillArr($count,$comp_tot_arr,$po_req);
        $comp_all_arr['net'].=$comp_val_arr['net'];
        $comp_all_arr['vat'].=$comp_val_arr['vat'];
        $comp_all_arr['nonvat'].=$comp_val_arr['nonvat'];
        $comp_all_arr['total'].=$comp_val_arr['total'];
        ResetValues($comp_val_arr);
        $count++;
        $io = 0;
        while($io<1){
            if($po_req==1){
                foreach (range('A', 'L') as $letra) {            
                    $sheet->setCellValue($letra.$count,'');            
                }
            }else{
                foreach (range('A', 'K') as $letra) {            
                    $sheet->setCellValue($letra.$count,'');            
                }
            }
            $io++;
            $count++;
        }
        if($po_req==1){
            $sheet->mergeCells('A'.($count-1).":L".$count);
        }else{
            $sheet->mergeCells('A'.($count-1).":K".$count);
        }

        
        $count++;
    }
}

function ResetValues(&$arr_vals){
    $arr_vals['net']='';
    $arr_vals['vat']='';
    $arr_vals['nonvat']='';
    $arr_vals['total']='';
}

?>
