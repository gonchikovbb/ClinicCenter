<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Hello!</title>
</head>
<body>
hello <?php echo $name ?>
<div class="container">
    <form action="/signUp" class="form-signup" method="POST">
        <h2>Registration</h2>
        <input type="text" name="first_name" class="form-control" placeholder="FirstName" required>
        <input type="text" name="second_name" class="form-control" placeholder="SecondName" required>
        <input type="text" name="third_name" class="form-control" placeholder="ThirdName">
        <input type="text" name="gender" class="form-control" placeholder="Gender" required>
        <input type="number" name="phone" class="form-control" placeholder="Phone" required>
        <input type="text" name="email" class="form-control" placeholder="Email" required>
        <input type="text" name="password" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
    </form>
</div>
</body>
</html>