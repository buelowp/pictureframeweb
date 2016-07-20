<!DOCTYPE html>
<html>
<head>
 <link rel="stylesheet" type="text/css" href="localstyle.css">
</head>
<title>Picture Frame Manager</title>
<body>

<h1>Manage Picture Frame contents</h1>
<h2>Select the frame, and manage the files that will be displayed.</h2>

<form action="uploadfor.php" method="post">
<h2>Select who to upload images for</h2>
<table id="select-form">
 <tr id=select-form">
  <td id=select-form">
   <select name="user" id="user-select">
    <option value="pete">Pete</option>
    <option value="dawn">Dawn</option>
   </select>
  </td>
  <td id="select-form"><input id="usersubmit" type="submit" value="Select User" name="submit"></td>
 </tr>
</table>
</form> 

</body>
</html>
