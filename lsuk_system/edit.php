<?php 
/** 
 * Multi Threading 
 * @author Bijaya Kumar 
 * @email it.bijaya@gmail.com 
 * @mobile +91 9911033016 
 * @link http://www.digitalwebsolutions.in 
**/ 

    // require class 
    require_once('./thread.class.php'); 
    
    // global function 
    include('functions/functions.php'); 
    
    // listen 
    mThread::listen(); 
    
    
    // start time 
    $time = time () ; 
                    
    // 
    $doSleep_response = NULL; 
    $response2 = NULL; 
                        
    // start thread #1, with receive return value with param value 10 
    mThread::start( array( 'doSleep', &$doSleep_response), 10 ,'bijaya' ) ; 
    
    //start thread #2, without receive return value with param value 10 
    mThread::start( 'doSleep1', 10 , 'kulvir' ) ; 
    
    // start thread #3, without receive return value with param value 10 
    mThread::start( 'doSleep2', 10 , 'ajit' ) ; 
        
    // running till completed 
    while ( mThread::runing () ) ; 
         
    
         
    echo "----------------------<br />" ; 
    echo "Response Return from doSleep <br />" ; 
    var_dump($doSleep_response); 
    echo "<br />----------------------<br />" ; 
             
    echo "====================<br /> Tooks " . (time () - $time ) . ' Secs. 3 threads,<br />====================' ; 
    
    die; 
?> 