<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
session_start();

if (!isset($_SESSION["IsLogin"])) {
    $_SESSION["IsLogin"] = 0; // chưa đăng nhập
}
if (!isset($_SESSION["Cart"])) {
    $_SESSION["Cart"] = array();
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>ĐIỆN THOẠI & MÁY TÍNH BẢNG</title>
        <link href="assets/ASSERS/font-awesome-4.3.0/css/font-awesome.min.css" rel="stylesheet" />
        <link href="assets/bootstrap-3.3.4/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="assets/ASSERS/style.css" rel="stylesheet" />
        <link href="tuyetroi.js" rel="stylesheet" />
        <script src="assets/ASSERS/js.js" type="text/javascript"></script>
        <script src="assets/ASSERS/jquery-1.11.3.min.js" type="text/javascript"></script>
        <script src="assets/ASSERS/jquery-2.1.4.min.js" type="text/javascript"></script>

        <script src="assets/ASSERS/bootstrap-3.3.4-dist/js/bootstrap.min.js" type="text/javascript"></script>

    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    
    </head>
    <body>
    <?php
    require_once 'utils/Context.php';
    require_once 'entities/User.php';
    require_once 'utils/Utils.php';
    require_once 'entities/User.php';
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
            elseif ($username =="" || $pwd == "" || $pwd1 == "" || $name == "" || $email == "" || $ngay == "" || $dienthoai == "" ){
               utils::Redirect("board.php?msg=6");
            }
            else if ($loi1 == 1){
                Utils::Redirect("board.php?msg=4");
            }
            elseif ($pwd <> $pwd1) {
                utils::Redirect("board.php?msg=7");
            }
            elseif ($captcha <> $_SESSION["captcha"]  ) {
                utils::Redirect("board.php?msg=5");
            }
           
            else{
                $u = new User($id, $username, $pwd, $name, $email, $ngay, $dienthoai, $permission, $GioiTinh);
                $u->insert();
                Utils::Redirect("board.php?msg=1");  // đăng ký thành công
            }
            
        }
     if(Context::IsLogged()) {	
            //Utils::Redirect("index1.php");
        }

        // xử lý đăng nhập

        else if(isset($_POST["btnDN"])) 
        {
            $TenDN = $_POST["txtUID"];
            $MK = $_POST["txtPWD"];

            $remember = isset($_POST["chkRememberMe"]) ? true : false;

            $p = new User(-1, $TenDN, $MK, "", "", time(), "", 0, "");

            $ret = $p->login();
            // $ret: true => đăng nhập thành công, $u có đủ thông tin
            // $ret: false => đăng nhập thất bại

            if ($ret) {
                 $_SESSION["IsLogin"] = 1; // đã đăng nhập
                $_SESSION["CurrentUser"] = (array) $p;

                    if ($remember) {
                        $expire = time()+ 15 * 24 * 60 * 60;
                        setcookie("UserName", $TenDN, $expire);
                    }
                $url = "index.php";
                Utils::Redirect($url);
            } else {
            	Utils::Redirect("board.php?msg=");
            }
        }
        //xu li gio hàng
         require_once './entities/Cart.php';

        if (isset($_POST["txtProId"])) {
            $proId = $_POST["txtProId"];
            $quantity = 1;

            Cart::addItem($proId, $quantity);
            Utils::Redirect("index.php");
        }
        ?>
                   
        <nav class="navbar navbar-inverse navbar-fixed-top" style="background-color:#dca7a7">
        <div class="container">
            <div class="navbar-header" >
                <a class="navbar-brand" href="index.php">
                    <i class="fa fa-home"></i>
                </a>
            </div>
              <!--Tìm kiếm-->
            <form class="navbar-form navbar-left" role="search" action="TimKiem.php">
                <div class="form-group" >
                    <input style="margin:3px" type="text" name="txtTim" class="form-control" placeholder="Nội dung tìm kiếm">
                </div>
                <button style="margin:3px" type="submit" name="btnTim" class="btn btn-default btn-danger"><i class="fa fa-search"></i></button>
            </form>     
              
                 <!--Đăng nhập thành công-->
            <ul class="nav navbar-nav navbar-right">
                <?php
                require_once 'utils/Context.php';
                require_once '/entities/Cart.php';
                if(Context::IsLogged()){
                    if (Context::getCurrentUser()["Permission"] == 0){
                        ?>
                    <li>
                        <a href="Shopping-cart.php">
                            <i class="fa fa-shopping-cart"></i>
                            Giỏ hàng đang có <?php echo Cart::count(); ?> sản phẩm
                        </a>
                    </li>
                    <li>
                        <a href="ThongTin.php">Hi <?php echo Context::getCurrentUser()["HoTen"]; ?></a>
                    </li>
                    <li>
                        <a href="Logout.php"><font color="primary">Thoát </font><i class="fa fa-sign-out"></i></a>
                    </li>
                    <?php
                    }
                    else {
                        utils::Redirect("QuanLy.php");
                    }
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
    
        //lam hinh chay qua chay lai
    <div class="container" style="width:1000px; height: 370px">
        <br>
        <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                <li data-target="#myCarousel" data-slide-to="1"></li>
                <li data-target="#myCarousel" data-slide-to="2"></li>
            </ol>
        //anh bia
        
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
                </div>
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
            require_once '/entities/nhasanxuat.php';
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
            <div class="panel panel-default" >
               <div class="panel-heading" style="background-color:#FFCACA">
                   <h3 class="panel-title">Sản Phẩm Mới Nhất</h3>
               </div>
                <div class="panel-body">
                    <div class ="row">
                        <?php
                        require_once 'entities/sanpham.php';
                        $list = sanpham::sanphamngaymoinhat();
                        if (count($list) == 0){
                             echo "<div class='col-md-12'>Không có sản phẩm!</div>";
                        }
                        else {
                            ?>
                            <form id="addToCart-form" method="post" action="">
                                <input type="hidden" id="txtProId" name="txtProId" />
                            </form>
                            <form id="addToID-form" method="post" action="">
                                <input type="hidden" id="txtId" name="txtId" />
                            </form>
                        <?php
                            for ($i = 0; $i < count($list); $i++){
                            ?>
                         <div class="col-sm-6 col-md-4">
                            <div class="thumbnail" style="height:370px">
                                <img src="img/sanpham/<?php echo $list[$i]->getId(); ?>/1.jpg"
                                     title="<?php echo $list[$i]->getTensanpham(); ?>" 
                                     alt="<?php echo $list[$i]->getTensanpham(); ?>" height="300" width="120">
                                <div class="caption">
                                    <h3><?php echo $list[$i]->getTensanpham(); ?></h3>
                                    <p>Giá tiền: <?php echo number_format($list[$i]->getDongia()); ?></p>
                                    <h4>Số lượt xem: <?php echo $list[$i]->getSoluotxem(); ?></h4>
                                    <h5><?php echo $list[$i]->getchitiet(); ?></h5>
                                </div>
                                <p>
                                    <a href="ChiTiet.php?id=<?php echo $list[$i]->getId(); ?>" name="btnCT" class="btn btn-primary" >
                                        <i class="fa fa-info"></i>
                                        Chi tiết
                                    </a>
                                    <?php
                                    if (Context::IsLogged()) {
                                        ?>
                                     <a id="btnAddToCart_<?php echo $list[$i]->getId(); ?>" href="#" role="button" data-proid="<?php echo $list[$i]->getId(); ?>" style="float: right">
                                            <img src="assets/img/dathang.png" width="130">
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </p>
                                </div>
                            </div>
                         <?php
                            }
                        }
                        ?>
                        </div>
                
                    </div>
                </div>
             <div class="panel panel-default" >
               <div class="panel-heading" style="background-color:#FFCACA">
                   <h3 class="panel-title">Sản Phẩm Bán Nhiều Nhất</h3>
               </div>
                <div class="panel-body">
                    <div class ="row">
                        <?php
                        require_once 'entities/sanpham.php';
                        $list = sanpham::spLuotBanNhieuNhat();
                        if (count($list) == 0){
                             echo "<div class='col-md-12'>Không có sản phẩm!</div>";
                        }
                        else {
                            for ($i = 0; $i < count($list); $i++){
                            ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="thumbnail" style="height:370px">
                                <img src="img/sanpham/<?php echo $list[$i]->getId(); ?>/1.jpg"
                                     title="<?php echo $list[$i]->getTensanpham(); ?>" 
                                     alt="<?php echo $list[$i]->getTensanpham(); ?>" height="300" width="120">
                                <div class="caption">
                                    <h3><?php echo $list[$i]->getTensanpham(); ?></h3>
                                    <p>Giá tiền: <?php echo number_format($list[$i]->getDongia()); ?></p>
                                    <h4>Số lượt xem: <?php echo $list[$i]->getSoluotxem(); ?></h4>
                                    <h5><?php echo $list[$i]->getchitiet(); ?></h5>
                                </div>
                                <p>
                                    <a href="ChiTiet.php?id=<?php echo $list[$i]->getId(); ?>" class="btn btn-primary" role="button">
                                        <i class="fa fa-info"></i>
                                        Chi tiết
                                    </a>
                                    <?php
                                    if (Context::IsLogged()) {
                                        ?>
                                        <a id="btnAddToCart_<?php echo $list[$i]->getId(); ?>" href="#" role="button" data-proid="<?php echo $list[$i]->getId(); ?>" style="float: right">
                                            <img src="assets/img/dathang.png" width="130">
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                            <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="panel panel-default" >
               <div class="panel-heading" style="background-color:#FFCACA">
                   <h3 class="panel-title">Sản Phẩm Xem Nhiều Nhất</h3>
               </div>
                <div class="panel-body">
                    <div class ="row">
                        <?php
                        require_once 'entities/sanpham.php';
                        $list = sanpham::spLuotXemNhieuNhat();
                        if (count($list) == 0){
                             echo "<div class='col-md-12'>Không có sản phẩm!</div>";
                        }
                        else {
                            for ($i = 0; $i < count($list); $i++){
                            ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="thumbnail" style="height:370px">
                                <img src="img/sanpham/<?php echo $list[$i]->getId(); ?>/1.jpg"
                                     title="<?php echo $list[$i]->getTensanpham(); ?>" 
                                     alt="<?php echo $list[$i]->getTensanpham(); ?>" height="300" width="120">
                                <div class="caption">
                                    <h3><?php echo $list[$i]->getTensanpham(); ?></h3>
                                    <p>Giá tiền: <?php echo number_format($list[$i]->getDongia()); ?></p>
                                    <h4>Số lượt xem: <?php echo $list[$i]->getSoluotxem(); ?></h4>
                                    <h5><?php echo $list[$i]->getchitiet(); ?></h5>
                                </div>
                                <p>
                                    <a href="ChiTiet.php?id=<?php echo $list[$i]->getId(); ?>" class="btn btn-primary" role="button">
                                        <i class="fa fa-info"></i>
                                        Chi tiết
                                    </a>
                                    <?php
                                    if (Context::IsLogged()) {
                                        ?>
                                        <a id="btnAddToCart_<?php echo $list[$i]->getId(); ?>" href="#" role="button" data-proid="<?php echo $list[$i]->getId(); ?>" style="float: right">
                                            <img src="assets/img/dathang.png" width="130">
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                            <?php
                            }
                        }
                        ?>
                    </div>
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


