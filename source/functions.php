<?
function login(){
		print "<td align='left' valign='top'>";
		
		print '<form action="?menu=1" method="post">';
		print "Zaloguj się lub <a href='rejestruj.php'>zarejestruj</a>";
		print '<input name="login" type="text" value="login" onclick=this.value="" />';
		print '<input name="password" type="password" value="xxx" onclick=this.value="" />';
		print '<input name="submit" type="hidden" />';
		print '</form>';
		print "</td>";
}

function Katalog(){ // Chyba nieużywana...
				global $searchWord;
				$searchWord = $_GET['s'];
				sortuj();
				global $sqlsort;
				global $sort;
				$querysort = mysql_query($sqlsort);
				print "<td align='left' valign='top'><table border='1'><tr><td><a href='?menu=1&Get_SortBy=id&Get_SortUpDown=".$sort;
				if(isset($_GET['s'])){
					print "&s=". $_GET['s'];
				}
				print "'>ID</a></td><td><a href='?menu=1&Get_SortBy=autor&Get_SortUpDown=".$sort."'>Autor</a></td><td><a href='?menu=1&Get_SortBy=tytul&Get_SortUpDown=".$sort."'>Tytuł</a></td><td>STAN</td>";
    	    	while ($row = mysql_fetch_assoc($querysort)) {
				  print "<tr><td>".$row["id"]."</td>";
				  print "<td>".$row["autor"]."</td>";
				  print "<td>".$row["tytul"]."</td>";
				  if($row['stan']==1 ){
					  print '<td><img src="img/kropka_zielona.png" /></td>';
				  } else {
					  print '<td><img src="img/kropka.png" /></td>';
				  }	
				  print "</tr>";
				}
				print "</table>";
}

function connect_db(){
	mysql_connect(DB_HOST, DB_USER, DB_PASS) or die ("bład 1");
	mysql_select_db(DB_DATABASE) or die ("bład 2");
}


function wyloguj(){
	if($_GET['logout']==1){
		session_destroy();
		header("Location: index.php?"); //menu=".$_GET['menu']);
	}
}
function menu(){
	print "<a href='?menu=1' class='menuLeftButtons'> <img src='img/katalog.png' /></a>";
	if($_SESSION['permission']<=1){
	print "<a href='?menu=2' class='menuLeftButtons'> <img src='img/dodaj2.png' /></a>";
	}
	if($_SESSION['permission']==0){
	print "<a href='?menu=3' class='menuLeftButtons'> <img src='img/uzytkownicy.png' /></a>";
	}
}

function books(){
	print '<form action="?menu=2" method="post">';
	print '<input name="autor" type="text" value="Autor" onclick=this.value="" /><br />';
	print '<input name="tytul" type="text" value="Tytuł" onclick=this.value="" /><br />';
	print '<input name="stan" type="hidden" value="1"  /><br />';
	/*print '<select name="stan">
			<option value="0">0</option>
			<option value="1">1</option>
			</select>';*/
	print '<input name="submit2" type="submit" value="Dodaj książkę" />';
	print '</form>';
	if($_POST['submit2']){
		$sql = "INSERT INTO `biblioteka`.`books` (`id`, `autor`, `tytul`, `stan`, `user_id`) VALUES (NULL, '".$_POST['autor']."', '".$_POST['tytul']."', '".$_POST['stan']."', NULL);";
		$query = mysql_query($sql);
		print "KSIĄŻKA DODANA";
		
	}	
	$sqlsort = "SELECT * FROM `books` ORDER BY `id` ASC";
	$querysort = mysql_query($sqlsort);
	print "<table border='1'><tr><td>ID</td><td>Autor</td><td>Tytuł</td><td>Stan</td><td>Usuń</td>";
	while ($rowbook = mysql_fetch_assoc($querysort)) {
    		    print "<tr><td>".$rowbook["id"]."</td>";
				print "<td>".$rowbook["autor"]."</td>";
    		    print "<td>".$rowbook["tytul"]."</td>";			
				print "<td>".$rowbook["stan"]."</td>";
				print "<td><a href='?menu=".$_GET['menu']."&delbook=".$rowbook["id"]."'>Usuń</a>";	
				print "</tr>";
	}
	print "</table>";
	if($_GET['delbook']){
	$sql = "DELETE FROM `biblioteka`.`books` WHERE `books`.`id` = '".$_GET['delbook']."';";
	mysql_query($sql);
	header("Location: index.php?menu=".$_GET['menu']);
	}
	
	
}

function users(){
	$sql_user = "SELECT * FROM `user` ORDER BY `id` ASC";
	$sql_user = mysql_query($sql_user);
	global $menu;
	print "<table border='1'>";
	while ($row = mysql_fetch_assoc($sql_user)) {

	  print "<tr><form action='' method='post'><input type='hidden' name='id' value='".$row["id"]."' /><td>".$row["id"]."</td>";
	  print "<td>".$row["login"]."</td>";
	  print "<td>".$row["name"]."</td>";
	  print "<td>".$row["sname"]."</td>";
	  print "<td><input size='1' type='text' name='perm' value='".$row["permission"]."' /><input type='submit' name='permsubmit' value='Zastosuj'/></td>";
	  print "<td>";
	  $sql_user_books = "SELECT * FROM `books` WHERE `user_id`='".$row['id']."';";
	  $sql_user_books = mysql_query($sql_user_books);
	  while ($row_user_books = mysql_fetch_assoc($sql_user_books)) {
		  print $row_user_books["tytul"].",";
	  }
	  print "</td></form></tr>";
	  if($_POST['permsubmit']){
		  $sql_perm="UPDATE `biblioteka`.`user` SET `permission` = '".$_POST['perm']."' WHERE `user`.`id` =  '".$_POST['id']."';";
		  mysql_query($sql_perm);
		  header('Location: ?menu='.$menu);
	  }
	}
	print "</table>";
}

function sortuj(){
				global $sqlsort;
				global $sort;
				global $Get_SortUpDown;
				global $Get_SortBy;
				global $searchWord;
				$Get_SortBy = $_GET['Get_SortBy'];
				$Get_SortUpDown = $_GET['Get_SortUpDown'];
				$searchWord = $_GET['s'];
				if($Get_SortBy=="tytul"){
					if($Get_SortUpDown=="desc"){
						$sort="asc";
						if(isset($searchWord)){
							$sqlsort  = "SELECT * FROM `books` WHERE `autor` LIKE '%".$searchWord."%' OR `tytul` LIKE '%".$searchWord."%' ORDER BY `tytul` ASC";
						} else {
							$sqlsort = "SELECT * FROM `books` ORDER BY `tytul` ASC";
						}
					} else {
						$sort="desc";
						if(isset($searchWord)){
							$sqlsort  = "SELECT * FROM `books` WHERE `autor` LIKE '%".$searchWord."%' OR `tytul` LIKE '%".$searchWord."%' ORDER BY `tytul` DESC";
						} else {
							$sqlsort = "SELECT * FROM `books` ORDER BY `tytul` DESC";
						}
					}
				} elseif($Get_SortBy=="autor") {
					if($Get_SortUpDown=="desc"){
						$sort="asc";
						if(isset($searchWord)){
							$sqlsort  = "SELECT * FROM `books` WHERE `autor` LIKE '%".$searchWord."%' OR `tytul` LIKE '%".$searchWord."%' ORDER BY `autor` ASC";
						} else {
							$sqlsort = "SELECT * FROM `books` ORDER BY `autor` ASC";
						}
					} else {
						$sort="desc";
						if(isset($searchWord)){
							$sqlsort  = "SELECT * FROM `books` WHERE `autor` LIKE '%".$searchWord."%' OR `tytul` LIKE '%".$searchWord."%' ORDER BY `autor` DESC";
						} else {
							$sqlsort = "SELECT * FROM `books` ORDER BY `autor` DESC";
						}
					}
				} else {
					if($Get_SortUpDown=="asc"){						
						$sort="desc";
						if(isset($searchWord)){
							$sqlsort  = "SELECT * FROM `books` WHERE `autor` LIKE '%".$searchWord."%' OR `tytul` LIKE '%".$searchWord."%' ORDER BY `id` DESC";
						} else {
							$sqlsort = "SELECT * FROM `books` ORDER BY `id` DESC";
						}
					} else {
						$sort="asc";
						if(isset($searchWord)){
							$sqlsort  = "SELECT * FROM `books` WHERE `autor` LIKE '%".$searchWord."%' OR `tytul` LIKE '%".$searchWord."%' ORDER BY `id` ASC";
						} else {
							$sqlsort = "SELECT * FROM `books` ORDER BY `id` ASC";
						}
					}
				} 
				return $sqlsort;
				return $sort;
}

?>