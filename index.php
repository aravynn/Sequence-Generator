<?php

/** 
 *
 * Sequence generation for the hose barcodes. 
 *
 */

require_once("class/sqlcontrol.php");  
include("functions.php");
require_once("class/userlogin.php"); 
require_once("class/generator.php");

$auth = new AuthenticateUser();
//$auth->GeneratePasswordHash('mepbro');

$didlogin = false;

if(isset($_POST['login'])){
	$didlogin = $auth->CheckPassword(FilterString($_POST['login']));
} else {
	$didlogin = $auth->CheckPassword('');
}

if(isset($_POST['logout']) && $_POST['logout'] == 'logout'){
	$auth->Logout();
	$didlogin = false;
}

if($didlogin){
	$number = new sequence();

	if(isset($_POST['serial']) && $_POST['serial'] !== ''){
		// something was posted.
		$TheLastInt = FilterInt($_POST['serial']);
		$output = $number->GetNextNumbers($TheLastInt);
	}
	
	if(isset($_POST['undocount']) && $_POST['undocount'] !== ''){
		$TheLastInt = FilterInt($_POST['undocount']);
		$output = $number->UndoLastAdd($TheLastInt);
	}
	
}
 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>MEP Brothers Hose Sequence Generator</title> 
		<link href="https://fonts.googleapis.com/css?family=Barlow+Semi+Condensed:300&display=swap" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<div id="wrapper">
		<img src="img/logo-color.svg" id="logo" />
		
		
		<?php IF( $didlogin ): ?>
			
			<?php
				if(isset($number)){
					echo '<div id="lastsaved">Last Saved Number: ' . $number->OutputCurrentNumber() . '</div>';
				}
			?>
			
			<form method="post" id="logout-form">
				<button type="submit" id="logout" name="logout" value="logout">logout</button>
			</form>
			
			<form method="post" id="getnumbers">
				<label for="serial">Serial Numbers Needed:</label><input type="number" id="serial" name="serial" placeholder="1" /><button type="submit">Get Numbers</button>
			</form>
			
			<?php 
				if(isset($TheLastInt)){
					if(isset($output)){
						$op = '<div id="numbers"><h2>Your Numbers</h2>';
				
						$op .= '<form method="post" id="undo">
							<input type="hidden" name="undocount" value="';
						
						if(isset($TheLastInt)){
							$op .= $TheLastInt; 
						} 
						
						$op .= '" />
							<button type="submit">Undo</button>
						</form><ul>';
						
						$first = true;
						
						foreach($output as $o){
							// place each number in a li
							if(!$first){
								$op .= '<li class="slash"> / </li>';
							} else {
								$first = false;
							}
							
							
							$op .= '<li>' . $o . '</li>';
						}
						
						$op .= '</ul></div>';
						
						echo $op;
					}
					
				}
			?>
			
		<?php ELSE: ?>
			<form method="post">
				<input type="password" id="login" name="login" placeholder="Enter Password" /><button type="submit">Login</button>
			</form>
		<?php ENDIF; ?>	
		
		
		</div>
	</body>
</html>