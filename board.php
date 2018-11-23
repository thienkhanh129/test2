<?php
session_start();

if (!isset($_SESSION["IsLogin"])) {
    $_SESSION["IsLogin"] = 0; // chưa đăng nhập
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Máy tính bản điện thoại</title>
    <link href="assets/ASSERS/font-awesome-4.3.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="assets/bootstrap-3.3.4/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/ASSERS/style.css" rel="stylesheet" />

    <script src="assets/ASSERS/js.js" type="text/javascript"></script>
    <script src="assets/ASSERS/jquery-1.11.3.min.js" type="text/javascript"></script>
    <script src="assets/ASSERS/jquery-2.1.4.min.js" type="text/javascript"></script>

    <script src="assets/ASSERS/bootstrap-3.3.4-dist/js/bootstrap.min.js" type="text/javascript"></script>

    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>

<?php
    require_once 'utils/Context.php';
    require_once 'entities/User.php';
    require_once 'utils/Utils.php';
    
    //Xử lý đăng ký
    if (isset($_POST["btnDK"])) {

            $captcha = $_POST["txtCaptcha"];
            
            $id = -1;
            $loi = 0;
            $username = $_POST["txtUser"];
            $pwd = $_POST["txtPass"];
            $pwd1 = $_POST["confipass"];
            $name = $_POST["txtFullName"];
            $email = $_POST["txtEmail"];
            $ngay = strtotime(str_replace('/', '-', $_POST["txtNgay"])); //d-m-Y
            $dienthoai = $_POST["txtDT"];
            $permission = 0;
            $GioiTinh = "";
            $loi = User::checkUserName($username);
            $loi1 = user::checkEmail($email);
            
            if($loi == 1) {
                Utils::Redirect("board.php?msg=3");
            }
            else if ($loi1 == 1){
                Utils::Redirect("board.php?msg=4");
            }
            elseif ($captcha <> $_SESSION["captcha"]  ) {
                utils::Redirect("board.php?msg=5");
            }
            elseif ($username =="" || $pwd == "" || $pwd1 == "" || $name == "" || $email == "" || $ngay == "" || $dienthoai == "" ){
                utils::Redirect("board.php?msg=6");
            }
            elseif ($pwd <> $pwd1) {
                utils::Redirect("board.php?msg=7");
            }
            else{
                $u = new User($id, $username, $pwd, $name, $email, $ngay, $dienthoai, $permission, $GioiTinh);
                $u->insert();
                Utils::Redirect("board.php?msg=1");  // đăng ký thành công
            }
            
        }
    // chuyển trang khác nếu đã đăng nhập
    if(Context::IsLogged()) {	
            //Utils::Redirect("index1.php");
        }

        // xử lý đăng nhập

        else if(isset($_POST["btnDN"])) 
        {
            $TenDN = $_POST["txtUID"];
            $MK = $_POST["txtPWD"];

            $Nho = isset($_POST["chkRememberMe"]) ? true : false;

            $p = new User(-1, $TenDN, $MK, "", "", time(), "", "");

            $ret = $p->login();
            // $ret: true => đăng nhập thành công, $u có đủ thông tin
            // $ret: false => đăng nhập thất bại

            if ($ret) {
                $_SESSION["IsLogin"] = 1; // đã đăng nhập
                $_SESSION["CurrentUser"] = (array) $p;

                if ($Nho) {
                    $expire = time() + 15 * 24 * 60 * 60;
                    setcookie("Username", $TenDN, $expire);
                }

                $url = "index.php";
                Utils::Redirect($url);
            } else {
            	Utils::Redirect("index.php");
            }
        }
        
?>
    
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" style="background-color:#dca7a7">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">
                    <i class="fa fa-home fa-lg"></i>
                </a>
            </div>
            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <input style="margin:3px" type="text" class="form-control" placeholder="Nội dung tìm kiếm">
                </div>
                <button style="margin:3px" type="submit" class="btn btn-default btn-primary"><i class="fa fa-search"></i></button>
            </form>
            <ul class="nav navbar-nav navbar-right">
                <?php
                require_once 'utils/Context.php';
                if(Context::IsLogged()){
                    ?>
                <li>
                    <a href="#">Hi <?php echo Context::getCurrentUser()["HoTen"]; ?></a>
                </li>
                <li>
                    <a href="Logout.php"><font color="primary">Thoát </font><i class="fa fa-sign-out"></i></a>
                </li>
                <?php
                }
                else{
                    ?>
                    <li>
                        <button style="margin:10px" type="button" class="btn btn-lg btn-default navbar-btn" 
                                        data-toggle="modal" data-target="#myModal1">
                            <span class="glyphicon glyphicon-log-in"></span>
                            <font color="black" size="2"> <b>Đăng nhập</b></font>
                        </button>
                    </li>	
                    <li>
                        <button style="margin:10px" type="button" class="btn btn-info btn-lg navbar-btn" data-toggle="modal" 
                                        data-target="#myModal">
                            <span class="glyphicon glyphicon-user"></span>
                            <font color="white" size="2"> <b>Đăng ký</b></font>
                        </button>
                    </li>
                <?php
                }
                ?>
                
            </ul>
        </div>
    </nav>
    
    <!--    from đăng nhập-->
    <div id="myModal1" class="modal fade " role="dialog">
	<form action="" method="POST" id="frmDN">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content col-sm-9">
                        <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h3 class="modal-title" align="center">Đăng nhập</h3>
                        </div>
                        <div class="form-group">
                        <input type="text" class="form-control input-lg" id="txtUID" name="txtUID" placeholder="Tên đăng nhập">
                        </div>
                        <div class="form-group">
                                <input type="password" class="form-control input-lg" id="txtPWD" name="txtPWD" placeholder="Mật khẩu">
                        </div>
                        <div class="form-group col-sm-7">
                                <label style="font-weight: normal">
                                        <input type="checkbox" name="chkRememberMe" /> Ghi nhớ
                                </label>
                        </div>
                        <div class="form-group" style="margin: 0 0 0 20px;">
                                <a href="#"> Quên mật khẩu </a>
                        </div>
                        <br/>
                        <div class="form-group">
                                <button type="submit" name="btnDN" class="btn btn-primary btn-lg btn-block">
                                <i class="fa fa-check"></i> Đăng Nhập</button>
                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                        </div>
                </div>
            </div>
        </form>
    </div>
    
    <!--    from đăng ký-->
    <div id="myModal" class="modal fade" role="dialog">
        <form action="" method="post" id="frmDK">
            <div class="modal-dialog">
		<!-- Modal content-->
                <div class="modal-content col-sm-9" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title" align="center">Đăng Ký</h3>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" class="form-control input-lg" name="txtUser" placeholder="Tên đăng nhập" onblur="" >
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control input-lg" name="txtPass" placeholder="Mật khẩu">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control input-lg" name="confipass" placeholder="Nhập lại mật khẩu">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control input-lg" name="txtFullName" placeholder="Họ Tên">
                    </div>
                    <div class="form-group">
                       <input type="date" class="form-control datepicker" name="txtNgay" placeholder="Ngày sinh">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control input-lg" name="txtEmail" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control input-lg" name="txtDT" placeholder="Điện thoại">
                    </div>
                   <div class="form-group">
                        <div class="col-sm-6">
                            <img src="lib/cool-php-captcha-0.3.1/captcha.php" id="captcha" name="captcha" style="cursor: pointer" />
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="txtCaptcha" name="txtCaptcha" placeholder="Mã xác nhận">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 15px">
                        <button type="submit" name = "btnDK" class="btn btn-primary btn-lg btn-block">
                            <i class="fa fa-check"></i> Đăng ký
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default"  data-dismiss="modal">Đóng</button>
                    </div>
                    
                </div>
            </div>
	</form>
    </div>
    
    <!--Hình ảnh-->
    <div class="container" style="width:1000px; height: 370px">
        <br>
        <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                <li data-target="#myCarousel" data-slide-to="1"></li>
                <li data-target="#myCarousel" data-slide-to="2"></li>
            </ol>
    
            <!-- Wrapper for slides -->
            <div class=”navbar-brand”>
                <div class="hinh">
                    <div class="carousel-inner" role="listbox">
                        <div class="item active" style="width: 700px; margin-left: 100px">
                            <img src="assets/img/HinhBia/15910146_1800600636857540_1532751772_n.jpg" alt="Chania"/>
                            <div class=" carousel-caption">
                            </div>
                        </div>
                        <div class="item" style="width: 700px; margin-left: 100px">
                            <img src="assets/img/HinhBia/15934521_1800600683524202_35989083_n.jpg" alt="Chania"/>
                            <div class=" carousel-caption">                       
                            </div>
                        </div>
                        <div class="item" style="width: 700px; margin-left: 100px">
                            <img src="assets/img/HinhBia/15934722_1800600663524204_443667852_n.png" alt="Chania"/>
                            <div class=" carousel-caption">                       
                            </div>
                        </div>
                        <div class="item" style="width: 700px; margin-left: 100px">
                            <img src="assets/img/HinhBia/15935839_1800600700190867_473907546_n.jpg" alt="Chania"/>
                            <div class=" carousel-caption">                       
                            </div>
                        </div>
                        <div class="item" style="width: 800px; margin-left: 100px">
                            <img src="assets/img/HinhBia/15941624_1800600583524212_1160456550_n.jpg" alt="Chania"/>
                            <div class=" carousel-caption">                       
                            </div>
                        </div>
                        <div class="item" style="width: 750px; margin-left: 100px">
                            <img src="assets/img/HinhBia/15942378_1800600490190888_1425289336_n.jpg" alt="Chania"/>
                            <div class=" carousel-caption">                       
                            </div>
                        </div>
    
                        </div>
                </di>
                </div>
    
            <!-- Left and right controls -->
            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
    <div id="sidebar row" >
        <!-- Thanh menu -->
       <div class="left">
            <a href="#" class="list-group-item list-group-item-danger">
                Hãng sản xuất
            </a>
            <div>
                <?php
                require_once 'entities/nhasanxuat.php';

                $list = nhasanxuat::loadAll();
                        
                for ($i = 0; $i < count($list); $i++) {
                    ?>
                    <a href="viewHangsanxuat.php?catid=<?php echo $list[$i]->getMaNSX(); ?>" class="list-group-item">
                        <?php echo $list[$i]->getTenNSX(); ?>
                    </a>
                    <?php
                }
                ?>
           </div>
             <a href="#" class=" list-group-item list-group-item-danger">
                Loại sản phẩm
            </a>
            <div class="list-group">
                <?php
                require_once 'entities/loaisanpham.php';

                $list = loaisanpham::loadAll();
                        
                for ($i = 0; $i < count($list); $i++) {
                    ?>
                <a href="viewSanPham.php?catid=<?php echo $list[$i]->getMaLoai(); ?>" class="list-group-item">
                        <?php echo $list[$i]->getTenLoai(); ?>
                    </a>
                    <?php
                }
                ?>
            </div>
            
        </div>
        <div class="col-md-10">
          <div class="panel panel-default">
                        <div class="panel-heading">
                            <!-- InstanceBeginEditable name="page-title" -->
                            Thông báo
                            <!-- InstanceEndEditable -->
                        </div>
                        <div class="panel-body">
                            <!-- InstanceBeginEditable name="page-content" -->
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if (isset($_GET["msg"])) {
                                        $msg = $_GET["msg"];
                                        switch ($msg) {
                                            case 1:
                                                echo "Đăng ký thành công. Mời đăng nhập.";
                                                break;
                                            case 2:
                                                echo "Cập nhật thành công.";
                                                break;
                                            case 3:
                                                echo "Tên đăng nhập đã tồn tại vui lòng nhập tài khoản khác.";
                                                break;
                                            case 4:
                                                echo "Email đã được sử dụng.";
                                                break;
                                            case 5:
                                                echo "Mã xác nhận không đúng.";
                                                break;
                                            case 6:
                                                echo "Vui lòng nhập đầy đủ thông tin.";
                                                break;
                                            case 7:
                                                echo "Hai mật khẩu không trùng nhau.";
                                                break;
                                            case 8:
                                                echo "Vui lòng nhập đầy đủ thông tin hoặc tài khoản mật khẩu không đúng.";
                                                break;
                                            default:
                                                echo "Chủ đề KHÔNG tồn tai.";
                                                break;
                                        }
                                    } else {
                                        echo "Chủ đề KHÔNG tồn tai.";
                                    }
                                    ?>  
                                </div>
                            </div>
                            <!-- InstanceEndEditable -->
                        </div>
                    </div>
       </div>
    </div>
    <script type="text/javascript">
        $('#captcha').on('click', function () {
                
//                document.getElementById('captcha').src = 'lib/cool-php-captcha-0.3.1/captcha.php?' + Math.random();

                var src = 'lib/cool-php-captcha-0.3.1/captcha.php?' + Math.random();
                $('#captcha').attr('src', src);
            });

//            giỏ hàng
        $('a[id*=btnAddToCart_]').on('click', function () {
            
            var proId = $(this).data('proid');
            $('#txtProId').val(proId);

            $('#addToCart-form').submit();
        });
        
    </script>
        
</body>
</html>
