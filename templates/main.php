<!DOCTYPE html>
<html>
	<head>
  <title><?=$page->titlePrefix.$page->title;?> - Houston Hare</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content=<?="'$page->description'";?> />

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content=<?="'$page->title'";?>>
    <meta itemprop="description" content=<?="'$page->description'"?>>
    <meta itemprop="image" content=<?="'$page->banner'";?>>

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@treetrnk">
    <meta name="twitter:title" content=<?="'$page->title'";?>>
    <meta name="twitter:description" content=<?="'$page->description'";?>>
    <meta name="twitter:creator" content="@treetrnk">
    <!-- Twitter summary card with large image must be at least 280x150px -->
    <meta name="twitter:image:src" content=<?="'$page->banner'";?>>

    <!-- Open Graph data -->
    <meta property="og:title" content=<?="'$page->title'";?> />
    <meta property="og:type" content="article" />
    <meta property="og:url" content=<?="'http://houstonhare.com$page->location'";?> />
    <meta property="og:image" content=<?="'$page->banner'";?> />
    <meta property="og:description" content=<?="'$page->description'";?> />
    <meta property="og:site_name" content="Stories by Houston Hare" />
    <meta property="article:published_time" content=<?="'$page->time'";?> />
    <meta property="article:modified_time" content=<?="'$page->time'";?> />
    <meta property="article:section" content="<?=$page->section?>" />
    <meta property="article:tag" content=<?="'$page->tags'";?> />
    <meta property="fb:admins" content="Facebook numberic ID" />

    <link rel="shortcut icon" href="/resources/images/favicon2.png" type="image/x-icon">
		<link href="/resources/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!---<script src="https://use.fontawesome.com/0dabb168cf.js"></script>--->

    <?php include "/srv/http/writing/resources/css/css.php" ?>

	</head>
	<body>

		<progress value="0" data-toggle="tooltip" data-placement="bottom" title="Reading Progress"></progress>

		<header>
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-sm-10 col-xs-10 site-title">
						<a href="/">
							<span class="small">Stories by</span>
							<h2>Houston Hare</h2>
						</a>
					</div>
					<nav class="col-md-8 col-sm-2 col-xs-2 navlinks small text-right">
						<i class="glyphicon glyphicon-menu-hamburger nav-menu hidden-lg hidden-md"></i>
						<ul class="navlinks hidden-sm hidden-xs">
              <li><a href="#" data-toggle="modal" data-target="#searchMod" id="searchbtn"> &nbsp;&nbsp; <i class="glyphicon glyphicon-search"></i> &nbsp;&nbsp; </a></li>
              <?=getTopNav();?>

            </ul>
					</nav>
				</div>
			</div>
		</header>
    <nav class="toggle-nav" id="toggle-nav">
      <form action="/search/" method="get"> 
        <div class="row">
          <div class="col-xs-12">
            <div class="input-group" id="searchtoggle">
              <input type="text" name="s" class="form-control" placeholder="Search" />
              <span class="input-group-btn">
                <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i>
              </span>
            </div>
          </div>
        </div>
      </form>
			<ul>
        <?=getSideNav();?>
			</ul>
		</nav>

    <?php //if ($page->type != 'page') { ?>
		<div class="jumbotron banner">
			<div class="container">
				<br /><br /><br /><br />
        <?=$page->booktitle;?>
        <!--
        -->
			</div>
    </div>
    <?php /*} else { ?>
    <div class='spacer'>&nbsp;</div>
    <?php }*/ ?>

		<!-- Modal -->
		<div class="modal fade" id="searchMod" tabindex="-1" role="dialog" aria-labelledby="searchModLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<form action="/search/" method="get">
							<div class="input-group">
								<input type="text" name="s" placeholder="Search" class="form-control" id="searchInput" />
								<span class="input-group-btn">
									<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i></button>
								</span>
							</div>
              <?=getAllTags();?>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="subscribeMod" tabindex="-1" role="dialog" aria-labelledby="subscribeModLabel">
			<form class="form" action="" method="post" id="subForm">
        <input type="hidden" name="subscribe" value="yes" />
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h3 class="modal-title" id="subscribeModLabel"><i class="glyphicon glyphicon-envelope"></i> Subscribe!</h3>
						</div>
						<div class="modal-body">
							<small><p>Subscribe now to receive updates every time a new chapter is posted.</p></small>
							<div class="row">
								<div class="col-xs-12">
									<small><label class="control-label">Email <span class="text-danger"><b>*</b></span></label></small>
									<input type="email" placeholder="example@email.com" name="email" class="form-control" maxlength="200" required />
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<small><label class="control-label">Name</label> (Optional)</small>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6">
									<input type="text" name="fname" placeholder="First" class="form-control" maxlength="50" />
								</div>
								<div class="col-xs-6">
									<input type="text" name="lname" placeholder="Last" class="form-control" maxlength="50" />
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<small><label class="control-label">&nbsp;</label></small>
                  <div class="coinhive-captcha" data-hashes="512" data-key="<?=config('coinhive-public');?>">
                    <div class="g-recaptcha" data-sitekey="<?=config('google-public');?>"></div>
                  </div>
								</div>
							</div>
            </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary">Subscribe</button>
						</div>
					</div>
				</div>
			</form>
		</div>

    <button type="button" class="close" id="sub-btn-close"><small><i class="glyphicon glyphicon-remove"></i></small></button>

    <button type="button" class="btn btn-primary" id="sub-btn" data-toggle="modal" data-target="#subscribeMod" title="Subscribe"><i class="glyphicon glyphicon-envelope"></i><span class="hidden-xs"> Subscribe</span></button>

		<section class="container content-wrapper">
      <?=$page->breadcrumbs();?>

      <?php 
        if (!empty($error)) {
          echo "<div class='alert alert-danger' role='alert'><b>Error!</b> $error</div>";
        } elseif (!empty($success)) {
          echo "<div class='alert alert-success' role='alert'><b>Success! </b>$success</div>";
        }

        include "$page->type.php";

      ?>

    </section>

		<footer class="text-muted small">
			<p class="text-center">&#169; Copyright 2017, Houston Hare</a>
      <p class="text-center">
        "<a href="http://www.newgrounds.com/art/view/llamareaper/fantasy-landscape-2" target="_blank">Fantasy Landscape</a>" 
        and "<a href="http://www.newgrounds.com/art/view/llamareaper/pumpkinbutts" target="_blank">Pumpkinbutts.</a>"
        by <a href="http://atthespeedof.newgrounds.com/" target="_blank">Jason Coates</a> are licensed under 
        <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank">CC BY-NC-SA 3.0</a>
        and <a href="https://creativecommons.org/licenses/by-nc/3.0/" target="_blank">CC BY-NC 3.0</a>, respectively
      </p>
      <p class="text-center">
        "<a href="https://www.reddit.com/r/wallpapers/comments/3npdce/1920x1080_october_forest_variant_in_comments/" target="_blank">October Forest (green variant)</a>" by Aaron Hoek is used with permission.
      </p>
		</footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://coinhive.com/lib/captcha.min.js" async></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="/resources/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<!--<script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>-->
    <script type="text/javascript">
			$(document).ready(function() {

				$('.nav-menu').click(function(e) {
					var $this = $(this);
					if ($this.hasClass('glyphicon-menu-hamburger')) {
						$(this).removeClass('glyphicon-menu-hamburger');
						$(this).addClass('glyphicon-remove');
						$('#toggle-nav').slideDown('fast');
					} else {
						$(this).addClass('glyphicon-menu-hamburger');
						$(this).removeClass('glyphicon-remove');
						$('#toggle-nav').slideUp('fast');
					}
				});

				$('i.dropdown').click(function(e) {
					e.preventDefault();
					var $this = $(this);
					if ($this.hasClass('glyphicon-menu-down')) {
						$this.parent('a').siblings('ul').slideDown();
						$this.removeClass('glyphicon-menu-down');
						$this.addClass('glyphicon-menu-up');
					} else {
						$this.parent('a').siblings('ul').slideUp();
						$this.removeClass('glyphicon-menu-up');
						$this.addClass('glyphicon-menu-down');
					}
				});

				$('section, .jumbotron, footer').click(function() {
					$('.nav-menu').addClass('glyphicon-menu-hamburger');
					$('.nav-menu').removeClass('glyphicon-remove');
					$('#toggle-nav').slideUp();
				});

        $("#searchMod").on("shown.bs.modal", function() {
          $("#searchInput").focus(); 
        });

        $(document).on('keydown', function(e) {
          //console.log(e.type);
          if (e.which == 37) {
            if ($("#prevPage").length != 0) {
              //console.log('prev');
              //$('#prevPage').trigger('click');
              window.location = $('#prevPage').attr('href');
            }
          } else if (e.which == 39) {
            if ($("#nextPage").length != 0) {
              //console.log('next');
              //$('#nextPage').trigger('click');
              window.location = $('#nextPage').attr('href');
            }
          }
        });

        $("#sub-btn-close").click(function() {
          $("#sub-btn-close").hide();
          $("#sub-btn").hide();
        });

				$(function () {
				  $('[data-toggle="tooltip"]').tooltip()
				})

        var postType = "<?=$page->type;?>";
  
        if (postType == "chapter" || postType == "post") {
          readProgress();

          $(window).on('resize', function() {
            readProgress();
          });
        }

        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })


          /*
        $("#subForm").submit(function(e) {
          if ($honeypot.is(':checked')) {
            //$("#subBtn").prop("disabled", true);
            e.preventDefault();
            alert("No robots allowed!");
          }
        });
           */

			});
        
			function readProgress() {
				var contentPos = $(".content").position().top;
				var contentSize = $(".content").height();
				var windowSize = $(window).height();
				var max = contentPos + contentSize - windowSize - 150;
				var value = $(window).scrollTop();

				if (max > 0) {
					$("progress").attr('max', max);
					$("progress").attr('value', value);
          var percent = Math.round(value/max*100);
          $("progress").attr('data-original-title', 'Reading Progress (' + percent + '%)');

					$(document).on('scroll', function() {
						value = $(window).scrollTop();
            if (value >= max) {
              value = max;
            }
            percent = Math.round(value/max*100);
            $("progress").attr('value', value);
            $("progress").attr('data-original-title', 'Reading Progress (' + percent + '%)');
					});
				}
      }

		</script>
	</body>
</html>
