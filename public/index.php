
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes. In case if your CURL is slow and is loading too much (Can be IPv6 problem)
error_reporting(E_ALL);
define('OAUTH2_CLIENT_ID', '781597589623144469');
define('OAUTH2_CLIENT_SECRET', '5Z7yQ6nndvX_U_BTeJ7vJM-Bh4usGnBn');
$authorizeURL = 'https://discordapp.com/api/oauth2/authorize';
$tokenURL = 'https://discordapp.com/api/oauth2/token';
$apiURLBase = 'https://discordapp.com/api/users/@me';
$revokeURL = 'https://discordapp.com/api/oauth2/token/revoke';
session_start();

if(get('action') == 'login') {
  $params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => 'https://crystalline-giddy-newsprint.glitch.me/',
    'response_type' => 'code',
    'scope' => 'identify guilds email'
  );
  // Redirect the user to Discord's authorization page
  header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
  die();
}
if(get('code')) {
    // Exchange the auth code for a token
    $token = apiRequest($tokenURL, array(
      "grant_type" => "authorization_code",
      'client_id' => OAUTH2_CLIENT_ID,
      'client_secret' => OAUTH2_CLIENT_SECRET,
      'redirect_uri' => 'https://crystalline-giddy-newsprint.glitch.me/',
      'code' => get('code')
    ));
    $logout_token = $token->access_token;
    $_SESSION['access_token'] = $token->access_token;
    header('Location: ' . $_SERVER['PHP_SELF']);
  }

if(get('action') == 'logout') {
    apiRequest($revokeURL, array(
        'token' => session('access_token'),
        'client_id' => OAUTH2_CLIENT_ID,
        'client_secret' => OAUTH2_CLIENT_SECRET,
      ));
    unset($_SESSION['access_token']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    die();
  }

function apiRequest($url, $post=FALSE, $headers=array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    if($post)
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $headers[] = 'Accept: application/json';
    if(session('access_token'))
      $headers[] = 'Authorization: Bearer ' . session('access_token');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    return json_decode($response);
  }
  function get($key, $default=NULL) {
    return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
  }
  function session($key, $default=NULL) {
    return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
  }
?>
<!DOCUTYPE HTML>
<html id="html">
  <head>
    <title></title>

    <!-- meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- import js-->
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>

    <!-- import css-->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css"
    />
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"
    />
    <link rel="icon" href="https://cdn.glitch.com/dfd04d00-93a4-44c9-84e6-356d7a2892f1%2Fqueue_music-white-36dp.svg?v=1604665823533">
  </head>
  <body>
    <ul id="slide-out" class="side-nav fixed z-depth-2 alt" style="color:white!important;">
      <li class="center no-padding">
        <div class="blurple ark-blurple white-text" style="height: 180px;">
          <div class="row">
            <img
              src="<?php $user = apiRequest($apiURLBase); echo 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . '.png';?>"
              class="user-img circle responsive-img"
            />
            <!-- https://images-ext-1.discordapp.net/external/WayQzrYuyCCljA_d6ZfgYGfY2BDBW0GvEos-TVbdnNQ/%3Fsize%3D1024/https/cdn.discordapp.com/icons/772550849893105686/4a08d0e4f82f136a17d56f5c5abd583b.webp-->
            <p id="name">
              <?php 
              if(session('access_token')) { $user = apiRequest($apiURLBase);echo 'Welcome, ' . $user->username . '';}else { echo 'Not Logged In';}
              ?>
            </p>
          </div>
        </div>
      </li>
      <li id="active">
        <a class="waves-effect" href="#!"><b>Dashboard</b></a>
      </li>

      <ul class="collapsible" data-collapsible="accordion">
        <li id="dash_users">
          <div id="dash_users_header" class="collapsible-header waves-effect">
            <b>Users</b>
          </div>
          <div class="collapsible-body text-whiter" style="color:white!important;">
            <ul class="lighter-alt" style="color:white!important;">
              <li id="users_seller">
                <a class="waves-effect text-whiter" style="text-decoration: none;" href="#!"
                  >Seller</a
                >
              </li>

              <li id="users_customer">
                <a class="waves-effect text-whiter" style="text-decoration: none;" href="#!"
                  >Customer</a
                >
              </li>
            </ul>
          </div>
        </li>

        <li id="dash_products">
          <div
            id="dash_products_header"
            class="collapsible-header waves-effect"
          >
            <b>Products</b>
          </div>
          <div id="dash_products_body" class="collapsible-body">
            <ul class="lighter-alt">
              <li id="products_product">
                <a class="waves-effect text-whiter" style="text-decoration: none;" href="#!"
                  >Products</a
                >
                <a class="waves-effect text-whiter" style="text-decoration: none;" href="#!"
                  >Orders</a
                >
              </li>
            </ul>
          </div>
        </li>

        <li id="dash_categories">
          <div
            id="dash_categories_header"
            class="collapsible-header waves-effect"
          >
            <b>Categories</b>
          </div>
          <div id="dash_categories_body" class="collapsible-body">
            <ul class="lighter-alt">
              <li id="categories_category">
                <a class="waves-effect text-whiter" style="text-decoration: none;" href="#!"
                  >Category</a
                >
              </li>

              <li id="categories_sub_category">
                <a class="waves-effect text-whiter" style="text-decoration: none;" href="#!"
                  >Sub Category</a
                >
              </li>
            </ul>
          </div>
        </li>

        <li id="dash_brands">
          <div id="dash_brands_header" class="collapsible-header waves-effect">
            <b>Brands</b>
          </div>
          <div id="dash_brands_body" class="collapsible-body">
            <ul class="lighter-alt">
              <li id="brands_brand">
                <a class="waves-effect text-whiter" style="text-decoration: none;" href="#!"
                  >Brand</a
                >
              </li>

              <li id="brands_sub_brand">
                <a class="waves-effect text-whiter" style="text-decoration: none;" href="#!"
                  >Sub Brand</a
                >
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </ul>
    <header>
      <ul class="dropdown-content" id="user_dropdown">
        <li><a class="black-text" href="<?php if(session('access_token')) { $user = apiRequest($apiURLBase);echo '#!';}else { echo '?action=login';}?>"><?php if(session('access_token')) { $user = apiRequest($apiURLBase);echo 'Profile';}else { echo 'Login';}?></a></li>
        
        <?php if(session('access_token')) { $user = apiRequest($apiURLBase);echo "<li><a class='black-text' href='?action=logout'>Logout</a></li>";}else { echo "";}?>
      </ul>

      <nav class="blurple" role="navigation">
        <div class="nav-wrapper">
          <a
            data-activates="slide-out"
            class="button-collapse show-on-"
            href="#!"
            ><img
              style="margin-top: 17px; margin-left: 5px;"
              src="https://res.cloudinary.com/dacg0wegv/image/upload/t_media_lib_thumb/v1463989873/smaller-main-logo_3_bm40iv.gif"
          /></a>

          <ul class="right hide-on-med-and-down">
            <li>
              <a
                class="right dropdown-button"
                href=""
                data-activates="user_dropdown"
                ><i class=" material-icons">account_circle</i></a
              >
            </li>
          </ul>

          <a href="#" data-activates="slide-out" class="button-collapse"
            ><i class="mdi-navigation-menu"></i
          ></a>
        </div>
      </nav>

      <nav>
        <div class="nav-wrapper blurple darker-blurple">
          <a style="margin-left: 20px;" class="breadcrumb" href="#!">Admin</a>
          <a class="breadcrumb" href="#!">Index</a>

          <div style="margin-right: 20px;" id="timestamp" class="right"></div>
        </div>
      </nav>
    </header>

    <main class="">
      <div class="row">
        <div class="col s6">
          <div style="padding: 35px;" align="center" class="card not-black">
            <div class="row">
              <div class="left card-title" style="color:white;">
                <b>User Information</b>
              </div>
            </div>

            <div class="row">
              <a href="#!" class="alt">
                <div
                  style="padding: 30px;"
                  class="alt col s5 waves-effect"
                >
                  <i
                    class="white-text text-lighten-1 large icon material-icons"
                    >people</i
                  >
                  <span class="white-text text-lighten-1"
                    ><h5>Users</h5></span
                  >
                </div>
              </a>
              <div class="col s1">&nbsp;</div>
              <div class="col s1">&nbsp;</div>

              <a href="#!" class="alt">
                <div
                  style="padding: 30px;"
                  class="alt col s5 waves-effect"
                >
                  <i
                    class="white-text text-lighten-1 large icon material-icons"
                    >people</i
                  >
                  <span class="white-text text-lighten-1"
                    ><h5>Customer</h5></span
                  >
                </div>
              </a>
            </div>
          </div>
        </div>

        <div class="col s6">
          <div style="padding: 35px;" align="center" class="card">
            <div class="row">
              <div class="left card-title" >
                <b>Midi Management</b>
              </div>
            </div>
            <div class="row">
              <a href="#!">
                <div
                  style="padding: 30px;"
                  class="alt col s5 waves-effect"
                >
                  <i class="white-text text-lighten-1 large material-icons"
                    >store</i
                  >
                  <span class="white-text text-lighten-1"
                    ><h5>Product</h5></span
                  >
                </div>
              </a>

              <div class="col s1">&nbsp;</div>
              <div class="col s1">&nbsp;</div>

              <a href="https://panel.helper.gg/" target="_blank">
                <div
                  style="padding: 30px;"
                  class="alt col s5 waves-effect"
                >
                  <i class="white-text text-lighten-1 large material-icons"
                    >assignment</i
                  >
                  <span class="white-text text-lighten-1"
                    ><h5>Requests</h5></span
                  >
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col s6">
          <div style="padding: 35px;" align="center" class="card">
            <div class="row">
              <div class="left card-title">
                <b>Brand Management</b>
              </div>
            </div>

            <div class="row">
              <a href="#!">
                <div
                  style="padding: 30px;"
                  class="alt col s5 waves-effect"
                >
                  <i class="white-text text-lighten-1 large material-icons"
                    >local_offer</i
                  >
                  <span class="white-text text-lighten-1"><h5>Brand</h5></span>
                </div>
              </a>

              <div class="col s1">&nbsp;</div>
              <div class="col s1">&nbsp;</div>

              <a href="#!">
                <div
                  style="padding: 30px;"
                  class="alt col s5 waves-effect"
                >
                  <i class="white-text text-lighten-1 large material-icons"
                    >loyalty</i
                  >
                  <span class="white-text text-lighten-1"
                    ><h5>Sub Brand</h5></span
                  >
                </div>
              </a>
            </div>
          </div>
        </div>

        <div class="col s6">
          <div style="padding: 35px;" align="center" class="card">
            <div class="row">
              <div class="left card-title">
                <b>Category Management</b>
              </div>
            </div>
            <div class="row">
              <a href="#!">
                <div
                  style="padding: 30px;"
                  class="alt col s5 waves-effect"
                >
                  <i class="white-text text-lighten-1 large material-icons"
                    >view_list</i
                  >
                  <span class="white-text text-lighten-1"
                    ><h5>Category</h5></span
                  >
                </div>
              </a>
              <div class="col s1">&nbsp;</div>
              <div class="col s1">&nbsp;</div>

              <a href="#!">
                <div
                  style="padding: 30px;"
                  class="alt col s5 waves-effect"
                >
                  <i class="white-text text-lighten-1 large material-icons"
                    >view_list</i
                  >
                  <span class="truncate white-text text-lighten-1"
                    ><h5>Sub Category</h5></span
                  >
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
<!-- #23272A -->
      <div
        class="fixed-action-btn click-to-toggle"
        style="bottom: 45px; right: 24px;"
      >
        <a class="btn-floating btn-large pink waves-effect waves-light">
          <i class="large material-icons">add</i>
        </a>
      </div>
    </main>

    <footer class="blurple page-footer">
      <div class="container">
        <div class="row">
          <div class="col s12">
            <h5 class="white-text">Server Stats</h5>
            <ul id="credits white" style="color:white;">
              <?php 

    $jsonIn = file_get_contents('https://discord.com/api/guilds/772550849893105686/widget.json');
    $JSON = json_decode($jsonIn, true);

    $membersCount = count($JSON['members']);

    echo "Members Online: <strong>" . $membersCount . "</strong>";
   ?><br><div id="smthn">
              
              </div>
            </ul>
          </div>
        </div>
      </div>
      <div class="footer-copyright" style="color:white!important;">
        <div class="container">
          <span
            >Made using
            <a
              style="font-weight: bold;"
              >PHP</a
            ></span
          >
        </div>
      </div>
    </footer>
  </body>
</html>
<script>
if( /Android|webOS|like Mac|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
 document.getElementById("html").innerHTML = `<style>
  body {
    width: 100%;
    min-height: 100vh;
    display: relative;
    margin: 0;
    padding: 0;
    background: -webkit-linear-gradient(-45deg, #183850 0, #183850 25%, #192C46 50%, #22254C 75%, #22254C 100%);
  }
  .wrapper {
    position: absolute;
    top: 50%;
    left: 50%;
    align-items: center;
    justify-content: center;
    -webkit-transform: translate(-50%, -50%);
    -moz-transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    -o-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    text-align: center;
  }
  h1 {
    color: white;
    font-family: arial;
    font-weight: bold;
    font-size: 50px;
    letter-spacing: 5px;
    line-height: 1rem;
    text-shadow: 0 0 3px white;
  }
  h4 {
    color: #f1f1f1;
    font-family: arial;
    font-weight: 300;
    font-size: 16px;
  }
  .button {
    display: block;
    margin: 20px 0 0;
    padding: 15px 30px;
    background: #22254C;
    color: white;
    font-family: arial;
    letter-spacing: 5px;
    border-radius: .4rem;
    text-decoration: none;
    box-shadow: 0 0 15px #22254C;
  }</style><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
  <div class="wrapper">
    <h1>TRY<br><br><br>AGAIN</h1>
    <h4>This webpage is not suitable for mobile devices. Try again on a Desktop based OS.</h4>
  </div>
</body>
</html>`;
}
</script>
<style>
header,
main,
footer {
  padding-left: 240px;
}

body {
  backgroud: white;
}

@media only screen and (max-width: 992px) {
  header,
  main,
  footer {
    padding-left: 0;
  }
}

#credits li,
#credits li a {
  color: white;
}

#credits li a {
  font-weight: bold;
}

.footer-copyright .container,
.footer-copyright .container a {
  color: #BCC2E2;
}

.fab-tip {
  position: fixed;
  right: 85px;
  padding: 0px 0.5rem;
  text-align: right;
  background-color: #323232;
  border-radius: 2px;
  color: #FFF;
  width: auto;
}

@font-face{
  src: url(https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/font/material-design-icons/Material-Design-Icons.woff)format("woff");
  font-family:BiggerMaterial;
}



* .name {
  padding-top:-20px;
}

@media (pointer:none), (pointer:coarse) {
  html:after{
    content:"This website is not avaliable for mobile devices";
  }
}
  
  .user-img{
    margin-top:5%;
              width: 100;
              height:100;
  }
  body, .alt{
    background-color:#2C2F33;
  }
  
  * .card{
    background:#23272A!important;
  }
  
  * .s5:hover{
    background:#7289DA!important;
  }
  
  * .card-title{
    color:white!important;
  }
  
  * b{
    color:white!important;
  }
  
  * .blurple{
    background-color:#7289DA!important;
  }
  
  * .darker-blurple{
    background-color:#6376b8!important;
  }
  
  * .lighter-alt{
    background-color:#23272A!important;
  }
  
  * .text-whiter{
    color:white!important;
  }
</style>