<?php

function notify($token,$title,$body,$extra_data){
		$ch = curl_init("https://fcm.googleapis.com/fcm/send");
		$header=array('Content-Type: application/json',
        "Authorization: key=AAAADpufTQE:APA91bHCXUrX_WLqzqqOVoViBKWMNtXFTOf_3dExAhBNkyaXFcNDtKIfi2F-vqXiRfJBKzk52oUfZKI-ZJRqR9QCH7wQ8ppSJkhptPChZH-qyZ3Lwu82ORR86-7G_b_qtZj56WXuZQdw");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_POST, 1);
		$rand_id=round(microtime(true));
		$data_array=array("click_action"=>"FLUTTER_NOTIFICATION_CLICK","status"=>"done","rand_id"=>$rand_id);
		if($extra_data){
			$data_array=array_merge($data_array,$extra_data);
		}
		$full_data = json_encode(array(
			"to" => $token,
			"notification"=>array("title"=>$title,"body"=>$body),
			"data"=>$data_array,
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		echo curl_exec($ch);
		curl_close($ch);
	}
$token1 = 'dzHyTFnJpUMLnMGY9uNw-T:APA91bGzrCq3FR0ojuaNfNw1pTyzcR30wxB4T43o2vJpiG04IiqpPm-u1GfU_Yf2bc6XLWljiN4vhuOgM22SQNwEEJHAxciADq3SXPgOmsA0IJ78gz4Q4D9GQjxvWopuBiyDgAoQuu5b';
$token2 = 'eWz8VX_-T2yTL2k2qC3KmZ:APA91bEUVENijPUIqeXgmXfDupejpX4td67_fhkiaxIYxExFzO1XEQ8RKKrmb1laf7i_5auoLAElCL6J3txW26Su5g1XJhmp9_HcBiko_BoaEL24BBlhriQFMAnYUXehttBa-8YG_NtQ';
$token3 = 'fBEfROb2S9W_Hu3H_HB4eo:APA91bHJJ0lYsreXVXbAxzszkcQRq_hHwW2GtZsFFWsd_8sJqBEhGPtpcT9kBKY98bZwpDG20hvp30I-27pxDx7YQXy4Kdo9hTqLcqLIbu7wDZBRsIwCfF3tUQNyCVI4rxskOjIVl8rD';
$token4 = 'fczwBTP8Rf6IQ77Qjc7F23:APA91bGvPRZrzvTmCQttjjzCPb4CP4iTiuI-fDN52hVOT2nsaDhJ47ltDazejaGXdyCorluUcleXDVYHOy2q3WPu4t2dl2tPLwG8HhqNxqutq_jrsB3cTnqTmx99M3Xuft-24t28CQkw';
$token5 = 'dXC6O0eFSDGT_o6DnHSmFl:APA91bHlvihvZIYuPlKYjCm8BudsnR7it27PN6fV7u1mrz1qLbkoUMywWOhMc8RT0-R9rWqyKAMh_dqhCkEorm1KBujEOKgfHRzDVA_8V71ODNDI2KB5b60bqxDUO0aPbDV_jn9Xh89F';
$token6 = 'cPYhja7HSVmqnPtixXnslR:APA91bHxJZNDufgLgggZb1whqL-WX6dA1OGDHpAuQDBrKdTXm4yQZlKryL4Z_jVFs8L2-3hMF7hOsdrnH4LptjyzZkNy5FYdLpP8T18aQT-f5-WSbHYJmqt4IZnSIWMoPGxx2uQ3WG99';
$token7 = 'cfmD8nT1QEG1-hEbTsz_K7:APA91bG3mYeld6KjR17Wg7VdBmkchPjTpVKZujqcCuUD18k5bBubAStUjmm4z12opZhWXp8x1HgOpFNThz9xXDvMf4ju22vZQq7yUHyf5jVhiOaWo5Vz-ZLq6Hbp5rzbWwhFUKldkIiu';
$token8 = 'APA91bGjvEaX0xc09WahMR7Cm32BcIFvCTBHj0UjkyaflZUwOUAhrhMN5MPDkbXqeSstyGRiqld2HUtUZB3suH_NDTsCIrOB0wZYn6Iyfoiq-bd7Qh0Uj7EVKwxhoyof-qi6HhfWWdIL';
$token9 = 'djatRB5DQxSgcp7N10ygpD:APA91bHfZztAsH-3Fgf9f7tvH2IWT3EwFhUVOtGT2vUCbp5xXonodcQ3O-xPhioUmbG_84BWgMsG4U6-H9aAtoKL08pVZhUCp8dAVnz7kR3UwkAg_vP_4BN7cU5K9aSrJKMFC2QT0lPT';
notify($token9 , 'missing doc' , 'missing documents',array("type_key"=>'md'));

?>