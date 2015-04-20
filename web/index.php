<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Biblioteka</title>
</head>

<body>

<?php
session_start();
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');
$submit = $_POST['submit'];
$login = $_POST['login'];
$password = $_POST['password'];
$ses_user_id = $_SESSION['user_id'];
$ses_login = $_SESSION['login'];
$ses_password = $_SESSION['password'];
$permission = $_SESSION['permission'];
$menu = $_GET['menu'];



require_once('config.php');
require_once('functions.php');

connect_db();






print "<table><tr>";
if(isset($ses_login)){
	print "<td align='left' valign='top'>";
	menu();
	print "</td>";
}
if(isset($ses_login)){
	
		
		$sql = "SELECT * FROM `user` WHERE `login` = '".$ses_login."' AND `password` = '".$ses_password."'";
		$query = mysql_query($sql);
		$ile_wynikow = mysql_num_rows($query);
		if($ile_wynikow>0){
			print '<a href="?logout=1">Wyloguj</a> '.$ses_login;;
			print "<td>";
			if($menu==1){
				sortuj();
				$querysort = mysql_query($sqlsort);
				print "<table border='1'><tr><td><a href='?menu=1&sortby=id&sorttype=".$sort."'>ID</a></td><td><a href='?menu=1&sortby=autor&sorttype=".$sort."'>Autor</a></td><td><a href='?menu=1&sortby=tytul&sorttype=".$sort."'>Tytuł</a></td><td>STAN</td><td>WYPOŻYCZ</td>";
				if($permission<1){
					print "<td>UŻYTKOWNIK</td></tr>";
				}
    	    	while ($row = mysql_fetch_assoc($querysort)) {
    		    print "<tr><td>".$row["id"]."</td>";
				print "<td>".$row["autor"]."</td>";
    		    print "<td>".$row["tytul"]."</td>";
				$result = mysql_query("SELECT `user_id` FROM `books` WHERE `user_id` = '".$ses_user_id."'");
				$num_rows = mysql_num_rows($result);
				if($num_rows<3 || $permission<=1){
					if($row['stan']==1 ){
						print '<td><font color="#33FF00">dostępna</font></td>';
						print '<td><a href=?menu='.$_GET["menu"].'&wypozycz='.$row["id"].'>WYPOŻYCZ</a></td>';
						
					} else {
						print '<td><font color="#FF0000">niedostępna</font></td>';
						if($permission>1){
							$sql_user = "SELECT * FROM `user` WHERE `id` = ".$row['user_id'];
							$query_user = mysql_query($sql_user);
							$row_user = mysql_fetch_assoc($query_user);		
							//print "<td>".$row_user["login"]."</td>";
							if($row_user['login']==$ses_login){
								print "<td><font color='#0000FF'>Posiadasz już <br> tę książkę</font></td>";
							} else {
								print '<td></td>';
							}
						} else {
							$sql_user = "SELECT * FROM `user` WHERE `id` = ".$row['user_id'];
							$query_user = mysql_query($sql_user);
							$row_user = mysql_fetch_assoc($query_user);		
							print "<td><a href='?menu=".$_GET['menu']."&stanreset=".$row['id']."'>NIEDOSTĘPNE</a></td>";
							print "<td>".$row_user["login"]."</td>";
							if($_GET['stanreset']){
							$sql = "UPDATE `biblioteka`.`books` SET `stan` = '1', `user_id` = '' WHERE `books`.`id` = '".$_GET['stanreset']."';";
							mysql_query($sql);
							header("Location: index.php?menu=".$_GET['menu']);
						}
							
						}
					}	
				} else {
					if($row['stan']==1 ){
			        	print '<td><font color="#33FF00">dostępna</font></td>';
						print "<td>Wypożyczyłeś za dużo</td>";
					} else {
						print '<td><font color="#FF0000">niedostępna</font></td>';
						print "<td>Wypożyczyłeś za dużo</td>";
					}
				}
		
				
				print "</tr>";
				
			}
			} elseif($menu==2){
				sortuj();
				menu2();
			} elseif($menu==3){
				menu3();
				  
			} 
		}
}



print "</td></tr></table>";
if(!isset($ses_login)){
	print "<table><tr>";
	login(); 	//Jeśli niezalogowany to wyświetl formularz
	
	listaKsiazek();
	print "</td></tr></table>";
}

wyloguj();

if(isset($_GET['wypozycz'])){
	$sql = "UPDATE `biblioteka`.`books` SET `stan` = '0', `user_id` = '".$ses_user_id."' WHERE `books`.`id` = '".$_GET['wypozycz']."';";
	mysql_query($sql);
	header("Location: index.php?menu=".$_GET['menu']);
}

if(isset($submit)){

	$sql = "SELECT * FROM `user` WHERE `login` = '".$login."' AND `password` = '".$password."'";
	$query = mysql_query($sql);
	$ile_wynikow = mysql_num_rows($query);
	if($ile_wynikow>0){
		$_SESSION['password'] = $password;
		$_SESSION['login'] = $login;
		$row_permission = mysql_fetch_assoc($query);
		$_SESSION['user_id'] = $row_permission['id'];
		$_SESSION['permission'] = $row_permission['permission'];
		$_SESSION['sort'] = 1;
		header("Location: index.php?menu=".$_GET['menu']);
	} else {
		print "Złe hasło lub login";
	}
}



?>

</body>
</html>