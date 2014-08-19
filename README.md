fake-database-mirroring-oracle-and-mysql-with-php
=================================================
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
