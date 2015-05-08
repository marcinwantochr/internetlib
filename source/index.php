<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
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

?>
<div id="container">
<a href="index.php"><div id="logo">
</div></a>
<div id="menuLeft">
<?
if(isset($ses_login)){
	print "Zalogowany jako: ".$ses_login;
	print '<a href="?logout=1" class="menuLeftButtons"><img src="img/wyloguj2.png"/></a>';
	menu();
} else {
	login();
}

?>
</div>
<div id="menuTop">
<img class="menuTop" src="img/informacje.png" /> <img class="menuTop" src="img/kontakt.png" /> <form class="menuTop" method="get"> <input type="text" name="s" /></form>
</div>
<div id="tabela">
<?
if(isset($ses_login)){
		$sql = "SELECT * FROM `user` WHERE `login` = '".$ses_login."' AND `password` = '".$ses_password."'";
		$query = mysql_query($sql);
		$ile_wynikow = mysql_num_rows($query);
} else {
	$permission = 4;
}
/*if(isset($searchWord)){
	$menu=4;
}*/
/*		if($ile_wynikow>0){
			//print '<a href="?logout=1">Wyloguj</a> '.$ses_login;;
*/			print "<td>";
			if($menu==1 or $menu==""){
				sortuj();
				$querysort = mysql_query($sqlsort);
				print "<table border='1'><tr><td><a href='?menu=1&Get_SortBy=id&Get_SortUpDown=".$sort;
				if(isset($_GET['s'])){
					print "&s=". $_GET['s'];
				}
				print "'>ID</a></td><td><a href='?menu=1&Get_SortBy=autor&Get_SortUpDown=".$sort;
				if(isset($_GET['s'])){
					print "&s=". $_GET['s'];
				}
				print "'>Autor</a></td><td><a href='?menu=1&sortby=tytul&Get_SortUpDown=".$sort;
				if(isset($_GET['s'])){
					print "&s=". $_GET['s'];
				} 
				print "'>Tytuł</a></td><td>Stan</td>";
				if($permission<4){
					print "<td>Wypożycz</td>";
				}
				if($permission<=1){
					print "<td>Użytkownik</td></tr>";
				}
    	    	while ($row = mysql_fetch_assoc($querysort)) {
    		    print "<tr><td>".$row["id"]."</td>";
				print "<td>".$row["autor"]."</td>";
    		    print "<td>".$row["tytul"]."</td>";
				
					
					$result = mysql_query("SELECT `user_id` FROM `books` WHERE `user_id` = '".$ses_user_id."'");
					$num_rows = mysql_num_rows($result);
					//if ($num_rows<$maxBooks){
						if($row['stan']==1 ){
							print '<td><img src="img/kropka_zielona.png" /></td>';
							if ($permission<4){
								if ($num_rows<$maxBooks || $permission<=1){
									print '<td><a href=?menu='.$_GET["menu"].'&wypozycz='.$row["id"].'>WYPOŻYCZ</a></td>';
									if($permission<=1){
										print "<td></td>";
									}
								} else {
										print '<td>'.$maxBooksInfo.'</td>';
								}
							}
						} else {
							print '<td><img src="img/kropka.png" /></td>';
							if($permission>1){
								$sql_user = "SELECT * FROM `user` WHERE `id` = ".$row['user_id'];
								$query_user = mysql_query($sql_user);
								$row_user = mysql_fetch_assoc($query_user);		
								//print "<td>".$row_user["login"]."</td>";
								if ($permission<4){
									if ($num_rows<$maxBooks){
										if($row_user['login']==$ses_login){
											print "<td><font color='#0000FF'>Posiadasz już <br> tę książkę</font></td>";
										} else {
											print '<td></td>';
										}
									} else {
										print '<td>'.$maxBooksInfo.'</td>';
									}
								}
							} else { //Sekcja Wypożycz dla admina 
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
				 /*else { //Informacja o przekroczonej ilości wypożyczonych książek
					if($row['stan']==1 ){
			        	print '<td><img src="img/kropka_zielona.png" /></td>';
						print "<td>".$maxBooksInfo."</td>";
					} else {
						print '<td><font color="#FF0000"><img src="img/kropka.png" /></font></td>';
						print "<td>".$maxBooksInfo."</td>";
					}
				}*/
		
				
				print "</tr>";
				
			}
			} elseif($menu==2){
				if($permission<2){
					sortuj();
					books();
				} else {
					print "Brak uprawnień";
				}
			} elseif($menu==3){
				if($permission<2){
					users();
				} else {
					print "Brak uprawnień";
				}
			} elseif($menu==4){ //wyszukiwarka
				$sqlSearch  = "SELECT * FROM `books` WHERE `autor` LIKE '%".$searchWord."%' OR `tytul` LIKE '%".$searchWord."%'";
				$sqlSearch = mysql_query($sqlSearch);
				print "<table border='1'>";
				while ($row = mysql_fetch_assoc($sqlSearch)) {
					print "<tr><td>".$row["id"]."</td>";
					print "<td>".$row["autor"]."</td>";
					print "<td>".$row["tytul"]."</td>";
					print "<td>".$row["stan"]."</td>";
				}
				print "</table>";
				
			}
/*		}
} else {
	Katalog();
}*/

?>
</div>
<?

print "</td></tr></table>";
/*
if(!isset($ses_login)){
	print "<div id='menuLeft'>";
	login(); 	//Jeśli niezalogowany to wyświetl formularz
	print "</div>";
	print "<div id='tabela'>";
	Katalog(); //Oraz listę książek
	print "</td></tr></table>";
	print "</div>";
}*/

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
		print $BadPass;
	}
}
print $_SESSION['permission'];
?>

</div>
</body>
</html>