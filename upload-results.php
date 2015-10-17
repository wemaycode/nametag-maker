<!DOCTYPE html>
<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once 'excel_reader2.php';
?>

<html lang='en' xml:lang='en' xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Highrock Nametag Maker</title>
	<link href='http://fonts.googleapis.com/css?family=Crimson+Text:400' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>        window.jQuery || document.write("<script src=includes/js/jquery.min.1.7.2.js'>\x3C/script>")</script>		
	<script src="includes/js/jquery.cookie.js"></script>
</head>
<link rel="stylesheet" href="includes/css/style.css"  type="text/css" />
<body>
<div id="content">
	<?php 
		//if they DID upload a file...
		if($_FILES['names']['name'])
		{
			//if no errors...
			if(!$_FILES['names']['error'])
			{
				$filename = $_FILES['names']['name'];

				// allowed extensions
				$allowed = array('xls');
				// get file extension
				$ext = pathinfo($filename, PATHINFO_EXTENSION);

				//now is the time to modify the future file name and validate the file
				$new_file_name = strtolower($_FILES['names']['tmp_name']); //rename file
				if($_FILES['names']['size'] > (1024000)) //can't be larger than 1 MB
				{
					$valid_file = false;
					$message = 'Oops!  Your file\'s size is too large.';					
				}
				if(!in_array($ext, $allowed)){
					$valid_file = false;
					$message = "Please use a XLS file (XLSX not allowed, sorry!)";
				}
				else {
					$valid_file = true;
				}
				//if the file has passed the test
				if($valid_file)
				{
					//move it to where we want it to be
					move_uploaded_file($_FILES['names']['tmp_name'], $new_file_name.".".$ext);
					$message = 'Congratulations!  Your file was accepted.';
				}
				else {
					$message = $message . " <a href='/'>Back</a>";
				}
				echo "<!--".$message."-->";
			}			
		}
	?>
	<?php 
		$data = new Spreadsheet_Excel_Reader($new_file_name.".".$ext);
		echo "<!--".$data->dump(true,true)."-->";

		// try to get an array of the parents and kids names

		/* array from comma sep values
		$str = $data->val(2,'D');
		$arr = explode(",",$str);
		print_r($arr);
		*/
		
		$maxrow = $data->rowcount($sheet_index=0); // rowcount, subtract header row				
		
		//echo "Row count = " . $maxrow;
		//echo "<br>Page count = " . $maxpage;

		// Put names and kids names into an array
		$names = array();
		for ($row = 1; $row < $maxrow+1; $row++)
		{			
			$array_row = array("fn" => ucfirst($data->val($row,'B')), "ln" => $data->val($row,'C'));

			$names[] = ($array_row);

			// add kids names, if cell is not empty
			if($data->val($row,'D') != "")
			{
				$kidsnames = explode(",",$data->val($row,'D'));
				for ($i = 0; $i < count($kidsnames); $i++)
				{
					$names[] = array("fn" => ucfirst($kidsnames[$i]), "ln" => $data->val($row,'C'));
					//echo "# kids = " . count($kidsnames);
				}
			}			
		}
		/* // output array
		print "<pre>";
		print_r($names);
		print "</pre>";
		*/

		$maxname = sizeof($names);
		$maxpage = ceil(($maxname)/6);	// 6 nametags per page; round up

		//echo "array size: " . $maxname . "<br/>";
		//echo "# pages: " . $maxpage . "<br/>";

		// loop through pages
		for($page = 1; $page < $maxpage+1; $page++)
		{
			//echo "<b>Page " . $page . "</b><br/>";			
			//print_r($names);
			
			// PRINT FRONT SIDE
			echo "<div class='image'>";
			
			// using a loop generate 6 nametags per page from array
			for($name = ($page-1)*6; $name < $page*6; $name++)				
			{
				//echo "name pos = " . $name . ", ";
				//echo $names[$name]['fn'] . "<br>";
				
				$namepos = $name - (6*($page-1)) + 1;
				if($namepos==1){ $rowclass = "one"; }
				if($namepos==2){ $rowclass = "two"; }
				if($namepos==3){ $rowclass = "three"; }
				if($namepos==4){ $rowclass = "four"; }
				if($namepos==5){ $rowclass = "five"; }
				if($namepos==6){ $rowclass = "six"; }

				//echo "name " . $name . ", pos " . $namepos . " -- " . $names[$name]['fn'] . " " . $names[$name]['ln'] . "<br/>";
				echo '<div class="nametag ' . $rowclass . ' reg"><div class="first">' . $names[$name]['fn'] . '</div><div class="last">'. $names[$name]['ln'] . '</div></div>';
				
			}
			echo "</div>";
			
			// PRINT BACK SIDE
			echo "<div class='image'>";
			
			// using a loop generate 6 nametags per page from array
			for($name = ($page-1)*6; $name < $page*6; $name++)				
			{
				//echo "name pos = " . $name . ", ";
				//echo $names[$name]['fn'] . "<br>";
				
				$namepos = $name - (6*($page-1)) + 1;
				if($namepos==1){ $rowclass = "two"; }
				if($namepos==2){ $rowclass = "one"; }
				if($namepos==3){ $rowclass = "four"; }
				if($namepos==4){ $rowclass = "three"; }
				if($namepos==5){ $rowclass = "six"; }
				if($namepos==6){ $rowclass = "five"; }

				//echo "name " . $name . ", pos " . $namepos . " -- " . $names[$name]['fn'] . " " . $names[$name]['ln'] . "<br/>";
				echo '<div class="nametag ' . $rowclass . ' reg"><div class="first">' . $names[$name]['fn'] . '</div><div class="last">'. $names[$name]['ln'] . '</div></div>';
				
			}
			echo "</div>";
		}

	?>
</div>

</body>
</html>