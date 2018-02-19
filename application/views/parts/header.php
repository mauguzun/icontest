
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js">
    </script>

    <script>
    	let base ="<?= base_url();?>"
    </script>
    <title>
        Signin Template for Bootstrap
    </title>

    <!-- Bootstrap core CSS -->

</head>

<body class="text-center">
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">

            <a class="navbar-brand" href="<?= base_url(); ?>">
                Project name
            </a>
           
            <? if(isset($user)) : ?>
            <ul class="nav navbar-nav">
            
	            <li class="active">
	            <a href="<?= base_url('auth/edit_user')?>">
	           		 <?= lang("edit_user_heading")." ,<b>".$user->first_name ."</b>" ?></a>
	           		 
	            </li>
	            <li><a href="<?= base_url('auth/logout')?>">Logout</a></li>
	           
            </ul>
			<? endif; ?>

        </div>
    </div>
</nav>

<!-- Fixed navbar -->
<div class="container" style="padding-top: 50px;">

