<?php
include("php/db.php");
session_start();
require_once("response.php");//include KooKoo library
$r=new Response(); //create a response object
$cd = new CollectDtmf();
if (isset($_REQUEST['event']) && $_REQUEST['event'] == 'NewCall')   
{
        $no=$_REQUEST['cid'];
        $result = mysqli_query($conn,"select * from user where  mobile='$no' ");
        $row_cnt = $result->num_rows;
        $row = mysqli_fetch_assoc($result);
        $userid=$row['id']; 
        if ($row_cnt==0)    //check if user registered
        {
             $r->addPlayText("Sorry. This number is not registered. Thank you for calling, have a nice day");
             $r->addHangup();
             $r->send();
             exit();
        }
		$_SESSION['id']=$row['id'];
		$_SESSION['score']=0;
		$_SESSION['cur']=0;
		$result = mysqli_query($conn,"select * from questions ORDER BY rand() limit 5");
		while($row = mysqli_fetch_row($result))
		{
		$que[]=$row;
		}
		$_SESSION['question'] = $que;
    $r->addPlayText(" Welcome to qui pho ."); // play questions
	
	$i=$_SESSION['cur'];
	
	$_SESSION['ans']=$que[$i][6];
		
			$cd->addPlayText($que[$i][1]);
			$cd->addPlayText(" 1  ".$que[$i][2]);
			$cd->addPlayText(" 2  ".$que[$i][3]);
			$cd->addPlayText(" 3  ".$que[$i][4]);
			$cd->addPlayText(" 4  ".$que[$i][5]);
			$cd->addPlayText("	 enter your answer ");
		
    $r->addCollectDtmf($cd);
}
elseif (isset($_REQUEST['event']) && $_REQUEST['event'] == 'GotDTMF') //input taken from user
{
	$mob=$_REQUEST['cid'];
		$ans=$_REQUEST['data'];
		if($ans==$_SESSION['ans'])
		{
			$_SESSION['score']++;
		}
		$_SESSION['cur']++;
        $i=$_SESSION['cur'];
		if($i==5)
		{
			$r->addPlayText(" you answered ".$_SESSION['score']." correctly ");
			$r->addPlayText(" Thanks for playing ");
			$u=$_SESSION['id'];$s=$_SESSION['score'];
			$result = mysqli_query($conn,"INSERT INTO quiz (userid,question) VALUES ($u,$s)");
			$r->addHangup();
			$r->send();
			$postdata = "name=quiPho&no=".$mob."&msg=you answered ".$_SESSION['score']." correctly . Call 04433012998 to continue playing.";
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL,'http://faltusms.tk/sendSms.php');  
			curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 			
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
			curl_setopt($ch, CURLOPT_VERBOSE, 1); 
			curl_setopt ($ch, CURLOPT_TIMEOUT, 60); 
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata); 
			curl_setopt ($ch, CURLOPT_POST, 1); 
			$result = curl_exec($ch);
			exit();
		}
		
		$r->addPlayText(" next question ");
		$que=$_SESSION['question'];
		$cd->addPlayText($que[$i][1]);
			$cd->addPlayText(" 1  ".$que[$i][2]);
			$cd->addPlayText(" 2  ".$que[$i][3]);
			$cd->addPlayText(" 3  ".$que[$i][4]);
			$cd->addPlayText(" 4  ".$que[$i][5]);
			$cd->addPlayText(" enter your answer ");
		$_SESSION['ans']=$que[$i][6];
		$r->addCollectDtmf($cd);
        
    }
else
{
    $r->addPlayText("Thank you for calling qui pho ");
    $r->addHangup(); //end call
}
$r->send();
?>
