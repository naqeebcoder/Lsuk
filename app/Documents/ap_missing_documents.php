<?php
include '../action.php';
//Api for missing documents
if(isset($_POST['ap_missing_documents'])){
    $json=(object) null;
        if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
            $json->ap_status="success";
            $record =$obj->read_all("*","interpreter_reg","id=".$_POST['ap_user_id']);
            $interpreter = $record->fetch_assoc();
            $json = [
                'missing' => [
                    'work_evid' => [
                        'file' => $interpreter['work_evid_file'],
                        'label' => 'Right to work evidence',
                        'work_evid_issue_date' => $interpreter['work_evid_issue_date'],
                        'work_evid_expiry_date' => $interpreter['work_evid_expiry_date']
                    ],
                    'applicationForm' => [
                        'file' => $interpreter['applicationForm_file'],
                        'label' => 'Application Form'
                    ],
                    'agreement' => [
                        'file' => $interpreter['agreement_file'],
                        'label' => 'Agreement'
                    ],
                    'dbs' => [
                        'file' => $interpreter['dbs_file'],
                        'label' => 'CRB/DBS',
                        'dbs_no' => $interpreter['dbs_no'],
                        'dbs_issue_date' => $interpreter['dbs_issue_date'],
                        'dbs_expiry_date' => $interpreter['dbs_expiry_date'],
                        'is_auto_renewal' => $interpreter['is_dbs_auto']
                    ],
                    'nrpsi' => [
                        'label' => 'NRPSI',
                        'nrpsi_number' => $interpreter['nrpsi_number']
                    ],
                    'translation_qualification' => [
                        'file' => $interpreter['int_qualification'],
                        'label' => 'Translation Qualification',
                    ],
                    'nrcpd' => [
                        'file' => $interpreter['nrcpd_file'],
                        'label' => 'NRCPD',
                    ],
                    'asli' => [
                        'label' => 'ASLI',
                        'asli_number' => $interpreter['asli_number']
                    ],
                    'id_doc' => [
                        'file' => $interpreter['id_doc_file'],
                        'label' => 'Identity Document',
                        'id_doc_no' => $interpreter['id_doc_no'],
                        'id_doc_issue_date' => $interpreter['id_doc_issue_date'],
                        'id_doc_expiry_date' => $interpreter['id_doc_expiry_date']
                    ],
                    'nin' => [
                        'file' => $interpreter['nin'],
                        'label' => 'National Insurance Number / UTR',
                        'ni' => $interpreter['ni']
                    ],
                    'acNo' => [
                        'acNo' => $interpreter['acNo'],
                        'label' => 'Bank Details',
                        'bnakName' => $interpreter['bnakName'],
                        'acName' => $interpreter['acName'],
                        'acntCode' => $interpreter['acntCode']
                    ],
                    'dps' => [
                        'file' => $interpreter['dps'],
                        'label' => 'DPSI'
                    ]

                ]
            ];

            
        }else{
            $json->msg="not_logged_in";
            $json->ap_status="failed";
        }
        header('Content-Type: application/json');
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>