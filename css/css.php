		<style>
      @font-face {
        font-family: 'Open Sans';
        src: url(/css/fonts/OpenSans-Regular.ttf);
      }
      @font-face {
        font-family: 'Montserrat';
        src: url(/css/fonts/Montserrat-Regular.ttf);
      }
      @font-face {
        font-family: 'Montserrat SemiBold';
        src: url(/css/fonts/Montserrat-SemiBold.ttf);
      }
      @font-face {
        font-family: 'Yesteryear';
        src: url(/css/fonts/Yesteryear-Regular.ttf);
      }
			header {
				background-color: #0b5394; /*let user set*/
				position: fixed;
        top: 0;
				width: 100%;
				-moz-box-shadow:    3px 3px 25px 1px #222;
				-webkit-box-shadow: 3px 3px 25px 1px #222;
				box-shadow:         3px 3px 25px 1px #222;
				z-index: 4;
				height: 68px;
			}
			header a:link, header a:visited, header a:active {
				color: #ffffff;
				text-decoration: none;
				text-transform: uppercase;
			}
			div.site-title {
				padding-top: 3px;
				padding-bottom: 5px;
				line-height: initial;
			}
			div.site-title a h2 {
				margin: 0;
        font-family: 'Yesteryear', cursive;
				text-transform: none;
        font-weight: normal;
        font-size: 1.8em;
			}
			.small {
				font-size: 14px;
				text-transform: none;
			}
			nav.navlinks i.nav-menu {
				font-size: 35px;
				line-height: 68px;
				color: #ffffff;
				cursor: pointer;
			}
			ul.navlinks {
				list-style: none;
				position: relative;
				float: right;
			}
			ul.navlinks a {
				display: block;
        font-family: 'Montserrat', sans-serif;
				line-height: 68px;
			}
			ul.navlinks li {
				margin-left: 10px;
				position: relative;
				float: left;
				padding: 0 5px;
			}
			ul.navlinks li:hover {
				background-color: rgba(256,256,256, .06);
			}
			ul.navlinks li ul {
				display: none;
				line-height: initial;
				position: absolute;
				float: left;
				left: 0;
				background-color: #222;
				-moz-box-shadow:    3px 3px 25px 1px #222;
				-webkit-box-shadow: 3px 3px 25px 1px #222;
				box-shadow:         3px 3px 25px 1px #222;
				padding: 0;
				min-width: 175px;
			}
			ul.navlinks li:hover ul {
				display: block;
			}
			ul.navlinks ul li {
				display: block;
				float: left;
				width: 100%;
        margin: 0px;
				text-align: left;
				padding: 7px;
			}
      ul.navlinks ul a {
				text-transform: none;
        font-family: 'Open Sans', sans-serif;
				line-height: normal;
      }
			ul.navlinks ul ul {
				top: 0px;
				left: 100%;
			}
			ul.navlinks ul ul li {
				z-index: 5;
			}
			header nav.navlinks ul.navlinks li ul li ul {
				display: none;
			}
			header nav.navlinks ul.navlinks li ul li:hover > ul {
				display:block;
			}
      #toggle-nav {
				background-color: #222222;
				min-width: 250px;
				position: fixed;
				top: 0;
				right: 0;
				height: 100vh;
				padding-top: 68px;
				z-index: 3;
				overflow-y: auto;
				overflow-x: hidden;
				white-space: nowrap;
				border-left: 1px solid #444;
				border-right: 1px solid #000;
				display: none;
			}
			#toggle-nav ul {
				list-style: none;
				padding: 0;
			}
			#toggle-nav li {
				color: #ffffff;
				line-height: 50px;
				margin: 0;
			}
			#toggle-nav a {
				display: block;
				padding: 0 10px;
				color: #dddddd;
				font-size: 14px;
        font-family: 'Motserrat', sans-serif;
			}
			#toggle-nav a:hover {
				text-decoration: none;
				background-color: rgba(256,256,256, .06);
			}
			#toggle-nav a:active #toggle-nav a:visited {
				text-decoration: none;
			}
			#toggle-nav ul ul, #toggle-nav ul ul ul {
				display: none;
        border-top: 1px solid #000;
        border-bottom: 1px solid #444;
        background-color: rgba(0, 0, 0, 0.25);
			}
      #toggle-nav a i.glyphicon {
        padding: 20px 20px 20px 5px;
      }
			div.jumbotron {
    <?="background-image: url($banner);";?>
				background-color: #ffffff;
				background-repeat: no-repeat;
				background-position: center center;
				background-size: cover;
				padding-top: 89px;
				height: 450px;
				z-index: 1;
			}
			body {
        background-color: <?=$theme['bodybg'];?>;
        color: <?=$theme['color'];?>;
				font-size: 175%;
        font-family: 'Open Sans', sans-serif;
        padding-top: 50px;
			}
      h1, h2, h3, h4 {
        font-family: 'Montserrat SemiBold', sans-serif;
        text-transform: uppercase;
      }
			div.jumbotron {
				margin-bottom: 0;
			}
			div.jumbotron h1 {
				display: inline;
				color: #ffffff;
        text-shadow: 0 0 30px #000000;
/*
				background: rgba(0, 0, 0, .4);
*/
				padding: 10px;
        font-family: 'Montserrat SemiBold', sans-serif;
				border-radius: 5px;
        text-transform: uppercase;
			}
			section.content-wrapper {
        background-color: <?=$theme['contentbg'];?>;
        color: <?=$theme['color'];?>;
				padding: 25px;
				margin-top: -50px;
				margin-bottom: 50px;
				-moz-box-shadow:    0 30px 50px 0 rgba(1,1,1,.15);
				-webkit-box-shadow: 0 30px 50px 0 rgba(1,1,1,.15);
				box-shadow: 0 30px 50px 0 rgba(1,1,1,.15);
        z-index: 3;
			}
      .content-wrapper h1 {
        font-size: 250%;
      }
      .content-wrapper h2 {
        font-size: 175%;
      }
      .content-wrapper h3 {
        font-size: 125%;
      }
      .content-wrapper h4 {
        font-size: 100%;
      }
      #theme-select {
        margin: 0 0 0 10px;
      }
      #theme-select a {
        padding: 0 11px 0 11px;
        margin-left: 5px;
        border-radius: 100px;
        -moz-box-shadow:    3px 3px 3px 1px rgba(0,0,0, .2);
        -webkit-box-shadow: 3px 3px 3px 1px rgba(0,0,0, .2);
        box-shadow:         3px 3px 3px 1px rgba(0,0,0, .2);
        cursor: pointer;
        font-size: 15px !important;
     }
      #light-theme {
        background-color: #ffffff;
        border: 2px solid #999999;
      }
      #dark-theme {
        background-color: #222222;
        border: 2px solid #eeeeee;
      }
      div.content p {
          line-height: 30px;
          margin-bottom: 25px;
      }
      @media only screen and (max-width: 400px) {
        section.content-wrapper {
          font-size: 80%;
        }
        div.content p {
          line-height: 20px;
        }
      }
			div.share-btns img {
				width: 32px;
				border: 0;
			}
			div.share-btns a {
				text-decoration: none;
			}
      ol.breadcrumb {
        <?=$theme['breadcrumb'];?>
      }
      ul.pager li a {
        <?=$theme['pager'];?>
      }
			footer {
				margin: 10px auto 50px auto;
        max-width: 600px;
			}
      footer a {
        color: #555555;
      }
      .panel {
        background-color: transparent;
      }
      #searchtoggle {
        margin: 10px;
      }
      #sub-btn {
        position: fixed;
        bottom: 15px;
        right: 20px;
        -webkit-box-shadow: 3px 3px 10px 1px rgba(0,0,0, .5);
        box-shadow:         3px 3px 10px 1px rgba(0,0,0, .5);
        z-index: 2;
        display: none;
      }
		</style>
