# dbconnect

Simple class to work with mysql.

Example
:
Initializing:

<?
  $configs = [
        'host' => 'localhost',
        'username' => 'username',
        'password' => 'pass1234',
        'db_name' => 'maindb'
  ];
  
  $db = new DbConnect($configs);
  
?>

1) Make a query:

<?


  $result = $db->query('CREATE TABLE users(id int,name varchar(255),surname varchar(255),email varchar(255),friends_count int);');

  /*

  $result = ['success' => 1] if there is no error

  $result = ['success' => 0, 'error'=>'Some mysql error'] if there is some mysql error

  */

?>

2) Select 

<?

  $users = $db->get('users',      // Table name
  
                    "name='Bob'", // Where string (without word 'Where')
  
                    10,           // Limit
    
                    'id DESC',    // Order
    
                    ['name', 'surname'] // Fields to select
  );
  
                    
  
  /*
  
  Example output of $users:
  
  [
  
    [
  
      'id'=>1,
  
      'name'=>'Bob',
  
      'email'=>''
  
    ], 
  
    [
  
      'id'=>2,
  
      'name'=>'Bob',
  
      'email'=>''
  
    ]
  
    
  
  ] 
  
  
  
  OR 
  
  
  ['success' => 0, 'error'=>'Some mysql error'] 
  if there is some mysql error
  
  OR 
  
  [] 
  if the table is empty
  */
  foreach ($user in $users) {
    echo $user['name'].' '.$user['surname'];
    echo '<br>';
  }
?>

3) Insert

<?

  $db->insert('users', ['name'=>'Alice', 'surname'=>'Smith']);  // Table name and array of insert params

?>

4) Update

<?

  $db->update->('users', ['name'=>'NotBob']); // Set all user's names to 'NotBob'

  $db->update->('users', ['name'=>'NotBob'], 'id=1', 10); // Set all user's names with id=1 with limit 10

?>

5) PlusOne (if you want to just insrement some parameter)

<?

  $db->plusOne('users', 'friends_count') // +1 to friends_count of all users

  $db->plusOne('users', 'friends_count', 'id=1', 10) // +1 to friends_count of users with id=1 with limit 10

  $db->plusOne('users', 'friends_count', 'id=1', 10, true) // -1 to friends_count of users with id=1 with limit 10

  

?>

6) Delete

<?

  $db->remove('users', 'id=1');

  $db->remove('users', 'name="Bob"', 10); // With limit 10

?>

7) Prepare string to use in WHERE string

<?

$name = $db->stringPrep('"name with" some bad symbols "'."'");

$db->get('users', "name='$name'", 10);

?>
