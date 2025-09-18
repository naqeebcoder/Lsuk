<?php 
   $order_id = '45132'; 
   $secret_key = substr(hash('sha256', 'a1zB9eT!9Xk2D7vJ0sT9H@3', true), 0, 16);
   $encrypted_usr = openssl_encrypt($order_id, 'aes-128-ctr', $secret_key, 0, '1234567891011121');
   $encrypted_usr = urlencode($encrypted_usr); 
   echo $encrypted_usr;
?>