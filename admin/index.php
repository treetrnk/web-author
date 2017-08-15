<!DOCTYPE html>
<html>
	<head>
		<title>Admin - The Writings of Nathan Hare</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<style>
			header {
				position: fixed;
				width: 100%;
				margin: 0;
			}
			header h1 {
				display: inline;
			}
			.logout {
				line-height: 63px;
			}
			.content-wrapper {
				padding-top: 125px;
			}
		</style>
	</head>
	<body>

		<header>
			<div class="container">
				<div class="row">
					<div class="col-xs-8">
						<h2>Admin Panel</h2>
					</div>
					<div class="col-xs-4 text-right logout">
						<a href="#"><i class="glyphicon glyphicon-user"></i> Logout</a>
					</div>
					<div class="col-xs-12">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#">Posts</a></li>
							<li class="disabled"><a href="#">Navigation</a></li>
							<li class="disabled"><a href="#">Users</a></li>
						</ul>
					</div>
				</div>
			</div>
		</header>

		<section class="container content-wrapper">
			<div class="row">
				<div class="col-xs-12 content">
					<a href="#" class="btn btn-primary"><i class="glyphon glyphicon-plus"></i> New Post</a><br /><br />
					<table class="table table-hover table-responsive">
						<thead>
							<tr>
								<th width="50%">Title</th>
								<th></th>
								<th>Posted</th>
								<th width="50"></th>
							</tr>
						</thead>
						<tbody>
							<th><a href="#">Chapter 1</a></th>
							<td><a href="#"><i class="glyphicon glyphicon-eye-open"></i> View Post</a></td>
							<td>1/1/2017 - 12:00 AM</td>
							<td>
								<a href="#" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
							</td>
						</tbody>
					</table>
				</div>
			</div>
		</section>

		<!-- jQuery -->
		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<!--<script src="http://code.jquery.com/jquery.js"></script>
		<script src="js/bootstrap.min.js"></script>-->
	</body>
</html>

