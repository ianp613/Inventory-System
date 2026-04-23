<?php
    session_start();
    !isset($_GET["loc"]) ? header("location: ../index.php") : null;
    !array_key_exists("userid",$_SESSION) ? header("location: ../index.php") : null; 
    $dashboard = $_GET["loc"] == "dashboard" ? true : false;
    $equipments = $_GET["loc"] == "equipments" ? true : false;
    $consumables = $_GET["loc"] == "consumables" ? true : false;
    $isp = $_GET["loc"] == "isp" ? true : false;
    $routers = $_GET["loc"] == "routers" ? true : false;
    $ipaddress = $_GET["loc"] == "ipaddress" ? true : false;
    $mac = $_GET["loc"] == "mac" ? true : false;
    $terminals = $_GET["loc"] == "terminals" ? true : false;
    $cctv = $_GET["loc"] == "cctv" ? true : false;
    $logs = $_GET["loc"] == "logs" ? true : false;
    $accounts = $_GET["loc"] == "accounts" ? true : false;
    $groups = $_GET["loc"] == "groups" ? true : false;   
    $_SESSION["auth"] ? null : header("location: login.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory System</title>
        <link rel="shortcut icon" href="../assets/img/logo-ico.png" type="image/x-icon">
        <link rel="stylesheet" href="../assets/fontawesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="../assets/css/datatables/datatables.min.css">
        <link rel="stylesheet" href="../assets/css/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/sole.splash/splash.css">
        <link rel="stylesheet" href="../assets/css/colorpicker.css">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link id="css_theme" rel="stylesheet" href="../assets/css/theme/Light.css">
    </head>
    <body class="theme-body theme-body-dark">
        <div class="d-flex sticky-top theme">
            <canvas hidden class="theme_background" id="theme_background">
                
            </canvas>
            <nav class="text-white sidebar p-3 theme-sidebar theme-sidebar-dark" id="sidebar">
                <div class="d-flex justify-content-center align-items-center bg-light p-2 rounded theme-sidebar-img theme-sidebar-img-dark">
                    <img id="sidebar_logo" src="../../assets/img/fposi-logo.png" style="width: 150px;">
                </div>
                <ul class="nav theme-nav-item theme-nav-item-dark flex-column">
                    <li class="nav-item mt-3">
                        <a href="?loc=dashboard" class="nav-link f-15 text-light">
                            <i class="fa fa-tachometer red-1 <?php $dashboard ?  printf("text-light rounded") :  null;?>" style="width: 13px;"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <hr style="margin-top: 6px;">
                    <div class="d-flex justify-content-start align-items-center">
                        <h5 class="ms-3 red-1 f-14 fwt-5">MENU</h5>    
                    </div>
                    <li class="nav-item mb-2">
                        <a href="?loc=equipments" class="nav-link f-15 text-light <?php $equipments ?  printf("bg-light text-dark rounded") :  null;?>">
                            <i class="fa fa-wrench red-1  <?php $equipments ?  printf("text-dark rounded") :  null;?>" style="width: 13px;"></i> <span>Equipments</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="?loc=consumables" class="nav-link f-15 text-light <?php $consumables ?  printf("bg-light text-dark rounded") :  null;?>">
                            <i class="fa fa-cubes red-1  <?php $consumables ?  printf("text-dark rounded") :  null;?>" style="width: 13px;"></i> <span>Consumables</span>
                        </a>
                    </li>
                    <li hidden class="g_menu nav-item mb-2">
                        <a href="?loc=isp" class="nav-link f-15 text-light <?php $isp ?  printf("bg-light text-dark rounded") :  null;?>">
                            <i class="fa fa-wifi red-1  <?php $isp ?  printf("text-dark rounded") :  null;?>" style="width: 13px;"></i> <span>ISP</span>
                        </a>
                    </li>
                    <li hidden class="g_menu nav-item mb-2">
                        <a href="?loc=routers" class="nav-link f-15 text-light <?php $routers ?  printf("bg-light text-dark rounded") :  null;?>">
                            <i class="fa fa-gears red-1 <?php $routers ?  printf("text-dark rounded") :  null;?>" style="width: 13px;"></i> <span>Routers</span>
                        </a>
                    </li>
                    <li hidden class="g_menu nav-item mb-2">
                        <a href="?loc=ipaddress" class="nav-link f-15 text-light <?php $ipaddress ?  printf("bg-light text-dark rounded") :  null;?>">
                            <i class="fa fa-map-marker red-1 <?php $ipaddress ?  printf("text-dark rounded") :  null;?>" style="width: 13px;"></i> <span>IP Address</span>
                        </a>
                    </li>
                    <li hidden class="g_menu nav-item mb-2">
                        <a href="?loc=mac" class="nav-link f-15 text-light <?php $mac ?  printf("bg-light text-dark rounded") :  null;?>">
                            <i class="fa fa-map-marker red-1 <?php $mac ?  printf("text-dark rounded") :  null;?>" style="width: 13px;"></i> <span>MAC Address</span>
                        </a>
                    </li>
                    <li hidden class="g_menu nav-item mb-2">
                        <a href="?loc=terminals" class="nav-link f-15 text-light <?php $terminals ?  printf("bg-light text-dark rounded") :  null;?>">
                            <i class="fa fa-desktop red-1 <?php $terminals ?  printf("text-dark rounded") :  null;?>" style="width: 13px;"></i> <span>Terminals</span>
                        </a>
                    </li>
                    <li hidden class="g_menu nav-item mb-2">
                        <a href="?loc=cctv" class="nav-link f-15 text-light <?php $cctv ?  printf("bg-light text-dark rounded") :  null;?>">
                            <i class="fa fa-video-camera red-1 <?php $cctv ?  printf("text-dark rounded") :  null;?>" style="width: 13px;"></i> <span>CCTV</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item mb-2">
                        <a href="#" class="nav-link f-15 text-light" data-bs-toggle="modal" data-bs-target="#general_search">
                            <i class="fa fa-search red-1" style="width: 13px;"></i> <span>Search</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="http://192.168.15.221:9000/views/ping.php?loc=ping" target="_blank" class="nav-link f-15 text-light">
                            <i class="fa fa-search red-1" style="width: 13px;"></i> <span>Ping</span>
                        </a>
                    </li> -->
                    <hr class="" style="margin-top: 6px;">
                    <div class="d-flex justify-content-start align-items-center">
                        <h5 class="ms-3 red-1 f-14 fwt-5">USERS</h5>    
                    </div>
                    <li class="nav-item mb-2">
                        <a href="?loc=accounts" class="nav-link f-15 text-light <?php $accounts ?  printf("bg-light text-dark rounded") :  null;?>">
                            <i class="fa fa-id-card red-1 <?php $accounts ?  printf("text-dark rounded") :  null;?>" style="width: 13px;"></i> <span>Accounts</span>
                        </a>
                    </li>
                    <li hidden class="g_op nav-item mb-2">
                        <a href="#" class="nav-link f-15 text-light" data-bs-toggle="modal" data-bs-target="#operate_as">
                            <i class="fa fa-users red-1" style="width: 13px;"></i> <span>Operate as</span>
                        </a>
                    </li>
                    <!-- <li hidden class="g_account nav-item mb-2">
                        <a href="?loc=groups" class="nav-link f-15 text-light <?php //$groups ?  printf("bg-light text-dark rounded") :  null;?>">
                            <i class="fa fa-users red-1 <?php //$groups ?  printf("text-dark rounded") :  null;?>" style="width: 13px;"></i> <span>Groups</span>
                        </a>
                    </li>     -->
                    <hr style="margin-top: 2px;">
                </ul>
            </nav>
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center shadow-sm p-3 pb-0 pt-0 theme-nav-top theme-nav-top-dark">
                    <h5 class="mt-1 text-secondary">
                        <?php 
                            if(isset($_GET["loc"])){
                                if($_SESSION["g_member"]){
                                    $color = "black";
                                    if(stripos($_SESSION["g_name"], "main") !== false || stripos($_SESSION["g_name"], "annex") !== false){
                                        $color = "#168897";
                                    }
                                    if(stripos($_SESSION["g_name"], "real") !== false){
                                        $color = "orange";
                                    }
                                    if(stripos($_SESSION["g_name"], "network") !== false){
                                        $color = "red";
                                    }
                                    if(stripos($_SESSION["g_name"], "wifi") !== false){
                                        $color = "green";
                                    }
                                    echo "<span style=\"color: ".$color.";\" class=\"fa fa-users\"></span> <span style=\"color: ".$color.";\">".$_SESSION["g_name"]." </span> | ";
                                }
                                if($_GET["loc"] == "dashboard"){
                                    echo "<span class=\"fa fa-tachometer\"></span> Dashboard";
                                }elseif($_GET["loc"] == "equipments"){
                                    echo "<span class=\"fa fa-wrench\"></span> Equipments";
                                }elseif($_GET["loc"] == "consumables"){
                                    echo "<span class=\"fa fa-cubes\"></span> Consumables";
                                }elseif($_GET["loc"] == "isp"){
                                    $_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"? printf("<span class=\"fa fa-wifi\"></span> Internet Service Provider") : printf("404");
                                }elseif($_GET["loc"] == "routers"){
                                    $_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"? printf("<span class=\"fa fa-gears\"></span> Routers") : printf("404");
                                }elseif($_GET["loc"] == "ipaddress"){
                                    $_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"? printf("<span class=\"fa fa-map-marker\"></span> IP Address") : printf("404");
                                }elseif($_GET["loc"] == "mac"){
                                    $_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"? printf("<span class=\"fa fa-map-marker\"></span> MAC Address") : printf("404");
                                }elseif($_GET["loc"] == "terminals"){
                                    $_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"? printf("<span class=\"fa fa-desktop\"></span> Terminals") : printf("404");
                                }elseif($_GET["loc"] == "logs"){
                                    echo "<span class=\"fa fa-list\"></span> Activity Logs";
                                }elseif($_GET["loc"] == "artisanry"){
                                    echo "<span class=\"fa fa-wrench\"></span> Artisanry";
                                }elseif($_GET["loc"] == "cctv"){
                                    $_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"? printf("<span class=\"fa fa-video-camera\"></span> CCTV") : printf("404");
                                }elseif($_GET["loc"] == "accounts"){
                                    echo "<span class=\"fa fa-id-card\"></span> Accounts";
                                }else{
                                    echo "404";
                                }
                            }else{
                                header("location: ../index.php");
                            }
                        ?>
                    </h5>
                    <div>
                        <div hidden id="inform" class="alert-info">
                            <span></span>
                            <h6 class="f-12 mb-0 pb-0">Theme changer in settings is now</h6>
                            <h6 class="f-12 mt-0 pt-0">available, try it now.</h6>
                            <button class="f-12 btn btn-sm alert-success btn-success" id="got_it">Got it, don't show again.</button>
                        </div>
                        <div id="option_drop" class="dropdown text-dark f-14 fwt-5">
                            <div type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <!-- User Name -->
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li id="account"><button class="dropdown-item f-13" ><span class="fa fa-user me-2 text-secondary" style="width: 12px;"></span> Account</button></li>
                                <li id="settings"><button class="dropdown-item f-13"><span class="fa fa-gears me-2 text-secondary" style="width: 12px;"></span> Settings</button></li>
                                <li id="activity_log"><button class="dropdown-item f-13"><span class="fa fa-list me-2 text-secondary" style="width: 12px;"></span> Activity Logs</button></li>
                                <li id="artisanry"><button class="dropdown-item f-13"><span class="fa fa-wrench me-2 text-secondary" style="width: 12px;"></span> Artisanry</button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li title="Long press L key" id="logout"><button class="dropdown-item f-13"><span class="fa fa-sign-out me-2 text-secondary" style="width: 12px;"></span> Log Out</button></li>
                            </ul>
                        </div>    
                    </div>
                    
                </div>
                <div class="main-content m-3 p-2">
                    <?php 
                        if($_GET["loc"] == "dashboard"){
                            include("dashboard/dashboard.php");
                        }elseif($_GET["loc"] == "equipments"){
                            include("equipments/equipments.php");
                        }elseif($_GET["loc"] == "consumables"){
                            include("consumables/consumables.php");
                        }elseif($_GET["loc"] == "ipaddress"){
                            if($_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"){
                                include("ipaddress/ipaddress.php");
                            }else{
                                include "404.php";
                            }
                        }elseif($_GET["loc"] == "mac"){
                            if($_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"){
                                include("mac/mac.php");
                            }else{
                                include "404.php";
                            }
                        }elseif($_GET["loc"] == "terminals"){
                            if($_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"){
                                include("terminals/terminals.php");
                            }else{
                                include "404.php";
                            }
                        }elseif($_GET["loc"] == "cctv"){
                            if($_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"){
                                include("cctv/cctv.php");
                            }else{
                                include "404.php";
                            }
                        }elseif($_GET["loc"] == "isp"){
                            if($_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"){
                                include("isp/isp.php");
                            }else{
                                include "404.php";
                            }
                        }elseif($_GET["loc"] == "routers"){
                            if($_SESSION["privileges"] == "Administrator" || $_SESSION["g_type"] == "IT"){
                                include("routers/routers.php");
                            }else{
                                include "404.php";
                            }
                        }elseif($_GET["loc"] == "logs"){
                            include("logs/logs.php");
                        }elseif($_GET["loc"] == "artisanry"){
                            include("artisanry/artisanry.php");
                        }elseif($_GET["loc"] == "accounts"){
                                include("administrator/accounts.php");
                        }else{
                            include "404.php";
                        }  
                    ?>
                </div>
            </div>
        </div>
        <h6 class="copyright f-10 text-secondary"></h6>
        <?php include("modals/dashboard.php"); ?>
        <?php include("modals/equipments.php"); ?>
        <?php include("modals/consumables.php"); ?>
        <?php include("modals/ipaddress.php"); ?>
        <?php include("modals/cctv.php"); ?>
        <?php include("modals/isp.php"); ?>
        <?php include("modals/routers.php"); ?>
        <?php include("modals/mac.php"); ?>
        <?php include("modals/logout.php"); ?>
        <?php include("modals/settings.php"); ?>
        <?php include("modals/accounts.php"); ?>
        <?php include("modals/logs.php"); ?>
        <?php include("modals/artisanry.php"); ?>
        <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
        <script src="../assets/js/popper/popper.min.js"></script>
        <script src="../assets/js/datatables/datatables.min.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="../assets/js/sole.splash/splash.js"></script>
        <script src="../assets/js/html5-qrcode/html5-qrcode.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="../assets/js/sole.js"></script>
        <script src="../assets/js/script.js"></script>
        <script src="../assets/js/theme.js"></script>
        <script src="../assets/js/dates.js"></script>
        <script src="../assets/js/dashboard.js"></script>
        <script src="../assets/js/equipments.js"></script>
        <script src="../assets/js/consumables.js"></script>
        <script src="../assets/js/ipaddress.js"></script>
        <script src="../assets/js/mac.js"></script>
        <script src="../assets/js/cctv.js"></script>
        <script src="../assets/js/routers.js"></script>
        <script src="../assets/js/isp.js"></script>
        <script src="../assets/js/accounts.js"></script>
        <script src="../assets/js/activity_log.js"></script>
        <script src="../assets/js/artisanry.js"></script>
        <script src="../assets/js/colorpicker.js"></script>
        <script src="../assets/js/modal_alert.js"></script>
    </body>
</html>
