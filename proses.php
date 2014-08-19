<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Update Data</title>
</head>
<?php
/*
	1. Connect to mysql and oracle
	2. Check value in column_name in mysql database
	3. Add them to array
	4. If array is not empty, check array length
		4.1. Then loop for N time until looping finished
		4.2. If data found (not in mysql database), insert it to mysql databse
	5. If array is empty, just select all data from oracle then insert to mysql database
	
	BENEFITS :
	1. You can add data from oracle to mysql, mysql to oracle, or etc
	2. It is splitted to 1000 value only, same as the default rule in database
	3. More speed than comparing one by one
	
	REQUIREMENTS :
	1. Unlimited php and database connection
	
	by : Sidik Hadi Kurniadi
	hope you can submit a bug to me https://www.facebook.com/sidikhadi
	
*/
ini_set('memory_limit','-1');
@set_time_limit(0);
@error_reporting(0);
mysql_connect("mysqlserver","mysqluser","mysqlpassword"); 

$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = XXX)(HOST = X.X.X.X)(PORT = XXXX)))(CONNECT_DATA=(SID=XXX)))" ;
$conn = ocilogon("orcluser","orclpassword",$db) ;
	mysql_select_db("mysqldb");
	$checkcolumn = 'SELECT column_name FROM tablename';
	$checkcolumn = mysql_query($checkcolumn) or die(mysql_error());
	$new_array = array();
	while ($data = mysql_fetch_array($checkcolumn))
		{
			array_push($new_array, $data['column_name']);
		}
	
	if (!empty($new_array))
		{
			$arr_size = sizeof($new_array);
			$limit = 999; //limit of IN or NOT IN value in database is 1000 (0 until 999)
			for($offset=0;$offset<$arr_size;$offset+=999)
				{
					$new_array_slice = array_slice($new_array, $offset, $limit);
					$limit2 = $offset + $limit;
					$qry = "SELECT column_name, column_name2, column_name3 from table_name
						WHERE column_name NOT IN ( '" . implode($new_array_slice, "', '") . "' )
						AND rownum > $offset
						AND rownum < $limit2
						ORDER BY column_name ASC
					";
					
					$stid = ociparse($conn,$qry);
					ociexecute($stid,OCI_DEFAULT);
					while (ocifetch($stid)) 
					{
						$column_name = ociresult($stid,1);
						$column_name2 = ociresult($stid,2);
						$column_name3 = ociresult($stid,3);
						
						$column_name = str_replace("'","^p",$column_name);//check and replace unneeded symbols that causing an error
						$column_name = str_replace('\\',"^b",$column_name);
						
						if (!empty($column_name))
							{
								$new++;
							}
						$insert = "INSERT INTO new_mysql_table (new_column_1, new_column_2, new_column_3)
						VALUES ('$column_name', '$column_name2', '$column_name3')";
						mysql_query($insert) or die(mysql_error());
					}
				}
		}
	else
		{
			$qry = "SELECT column_name, column_name2, column_name3 from table_name ORDER BY column_name3 ASC, column_name2 ASC";
			$stid = ociparse($conn,$qry);
			ociexecute($stid,OCI_DEFAULT);
			while (ocifetch($stid)) 
			{
				$new++;
				$column_name = ociresult($stid,1);
				$column_name2 = ociresult($stid,2);
				$column_name3 = ociresult($stid,3);
				
				$column_name = str_replace("'","^p",$column_name);
				$column_name = str_replace('\\',"^b",$column_name);
				
				$insert = "INSERT INTO new_mysql_table (new_column_1, new_column_2, new_column_3)
				VALUES ('$column_name', '$column_name2', '$column_name3')";
				mysql_query($insert) or die(mysql_error());
			}
		}

	if ($new > 0)
		{
			echo "New data : ".$new."<br>";
		}
	if ($new == 0)
		{
			echo "No new data<br>";
		}
	mysql_close();
?>
</html>
