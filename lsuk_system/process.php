<?php

include 'db.php'; 
include 'class.php'; 
session_start();

 if(isset($_POST['add_receivable']) and $_POST['add_receivable']!=''){
  	$table='receivable';
  	$edit_id=$_POST['edit_id'];
  	$amount=$_POST['amount']?:0;  
  	$details=$_POST['details']; 
  	$given_by=$_POST['given_by']; 
  	$received_by=$_SESSION['userId']; 
  	$received_date=$_POST['received_date']; 
  	$attachment=$_POST['attachment']; 
  	$receivable_id=$_POST['receivable_id']; 
  	if(trim($_POST['loadFiless'])=='Loans'){
  		$terms=$_POST['terms']; 
      $balance=$_POST['amount']; 
  		$installments=$_POST['installments']; 
  		
  	} else{
  		$terms='0'; 
  		$installments='0'; 
  	  $balance=0;
  	}

    if(isset($_FILES["file"]["name"]) and $_FILES["file"]["name"]!=''){

        unlink('file_folder/receivable/'.$attachment);
        
         $picName=$acttObj->upload_file("receivable",$_FILES["file"]["name"],$_FILES["file"]["type"],$_FILES["file"]["tmp_name"],$_FILES["file"]['name']);

        $acttObj->editFuninstallments($table,$edit_id,'attachment',$_FILES["file"]["name"]);        
        $attachment=$picName;
    }else{
            $attachment = $attachment;
    }


    $lastid = $acttObj->update($table,array('amount'=>$amount,
                                   'details'=>$details,
                                   'given_by'=>$given_by,
                                   'received_by'=>$received_by,
                                   'balance'=>$balance,
                                   
                                   'received_date'=>$received_date,
                                   'terms'=>$terms,
                                    'installments'=>$installments,
                                   'receivable_id'=>$receivable_id, 
                                   'attachment'=>$attachment
                                    ),array('id' => $edit_id));

    $_SESSION['success'] ="Receivable has been updated successfully !";

    header('location:receivable_edit.php?edit_id='.$edit_id);


}

if(isset($_POST['partial_submit_add'])){
    
    $amount=$_POST['amount']; 
    $paid_date=$_POST['paid_date'];
    $loan_id=$_POST['loan_id'];
    $ramount=$_POST['ramount'];
    
    $insert_array=array('amount'=>$amount,'paid_date'=>$paid_date,'dated'=>date('Y-m-d'),'loan_id'=>$loan_id);

    // $row_part=$acttObj->read_specific('sum(amount) as amount','loan_repay',' status="1" and    loan_id='.$loan_id);
    if($_POST['amount']>$ramount){
        
        $_SESSION['error'] = "Receivable amount already reached to installments value !";
    }else{
       

         $acttObj->insert('loan_repay',$insert_array) ;
          $_SESSION['success'] = "Receivable amount added successfully !";     
         
    }
      
      header('location:receivable_partail.php?row_id='.$loan_id);exit;
       
}

if(isset($_POST['partial_submit_edit'])){
  $id=$_POST['id']; 
  $amount=$_POST['amount']; 
  $paid_date=$_POST['paid_date'];
  $loan_id=$_POST['loan_id'];
  $ramount=$_POST['ramount'];
 if($_POST['amount']>$ramount){
    $_SESSION['error'] = "Receivable amount already reached to installments value !";

 }else{
   

  $update_array=array('amount'=>$amount,'paid_date'=>$paid_date,'dated'=>date('Y-m-d'),'loan_id'=>$loan_id);

   $update_param=array('id'=>$id);
   $acttObj->update('loan_repay',$update_array,$update_param);
   $_SESSION['success'] = "Receivable amount updated successfully !";   

 }      
   header('location:receivable_partail.php?row_id='.$loan_id.'&id='.$id.'&edit=1');exit;
             
                 
}


if(isset($_GET['del']) && isset($_GET['id'])){

  $id= $_GET['id'];
  $loan_id= $_GET['row_id'];
  $acttObj->delFun('loan_repay',$id);
  $_SESSION['success'] = "Receivable amount delete  successfully !";   
  header('location:receivable_partail.php?row_id='.$loan_id);exit;
       
}