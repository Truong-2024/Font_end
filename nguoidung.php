    <?php
    session_start(); // Khởi động lại phiên

    // Kiểm tra xem người dùng đã đăng nhập chưa
    if (!isset($_SESSION['email'])) {
        header("location: login.php"); // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập
        exit;
    }

    // Xử lý đăng xuất
    if (isset($_GET['logout'])) {
        // Xóa tất cả các biến phiên
        $_SESSION = array();

        // Hủy phiên
        session_destroy();

        // Chuyển hướng về trang đăng nhập
        header("location: login.php");
        exit;
    }
    ?>
    <?php
    // Kết nối đến cơ sở dữ liệu
    require "conn.php";
    // Thực hiện truy vấn để lấy thông tin sản phẩm
    $query = "SELECT * FROM sanpham";
    $result = mysqli_query($conn, $query);

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Người dùng</title>
        <link rel="stylesheet" href="../CSS/styles.css">
        <link rel="stylesheet" href="../CSS/nguoidung.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <?php require "menu.php"; ?>
        
    </head>
    <body>
        <div class="logo-body">
            <img src="../Image/LOGO/20240221_SRHDip8f.jpg" alt="">
        </div>
        <div class="title">
            <h1>DANH SÁCH CÁC SẢN PHẨM</h1>
        </div>
        <ul class="products">
            <?php
            // Lặp qua kết quả truy vấn và hiển thị thông tin sản phẩm
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <li class="product-list-item">
                    <div class="product-item">
                        <div class="product-top">
                            <a href="chitietsanpham.php?id=<?php echo $row['MaSanPham']; ?>" class="product-thumb">
                                <img src="<?php echo $row['HinhAnh']; ?>" alt="">
                            </a>
                            <a href="chitietsanpham.php?id=<?php echo $row['MaSanPham']; ?>" class="buy-now">Mua ngay</a>
                        </div>
                        
                        <div class="product-infor">
                            <a href="chitietsanpham.php?id=<?php echo $row['MaSanPham']; ?>" class="product-cat"></a>
                            <a href="chitietsanpham.php?id=<?php echo $row['MaSanPham']; ?>" class="product-name"><?php echo $row['TenSanPham']; ?></a>
                            <div class="product-price">
                                <?php echo "<div>".$row["Gia"]."đ</div>"; ?>
                            </div>

                        </div>
                    </div>
                </li>
            <?php
            }
            ?>
        </ul>
        <?php require "footer.php"; ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="../JS/trangchu.js"></script>
    </body>
    </html>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var loginButton = document.querySelector(".giohang-dangnhap-dangky .login-logout");
            var registerButton = document.querySelectorAll(".giohang-dangnhap-dangky .login-logout")[1]; // Sử dụng index 1 vì nút "Đăng ký" là nút thứ hai trong danh sách
        
            loginButton.addEventListener("click", function() {
                window.location.href = "login.php"; // Chuyển hướng đến trang đăng nhập (login.php)
            });

            registerButton.addEventListener("click", function() {
                window.location.href = "signup.php"; // Chuyển hướng đến trang đăng ký (signup.php)
            });
        });
        </script>
        <script>
            $(document).ready(function(){
                //Tìm tất cả các li có sub-menu và thêm vào nó class has-child
                $('.sub-menu').parent('li').addClass('has-child');
            });
            //Cuộn trang giữ menu 
            $(document).ready(function(){
                $(window).scroll(function(){
                    if($(this).scrollTop()){
                        $("header").addClass('sticky');
                    }
                    else{
                        $("header").removeClass('sticky');
                    }
                });
            });  
        </script>
    <script>
    // Lắng nghe sự kiện khi trang tải hoàn tất
        document.addEventListener("DOMContentLoaded", function() {
            // Lấy tham số id từ URL
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');

            // Nếu có id sản phẩm được chọn từ URL, ẩn tất cả sản phẩm khác và chỉ hiển thị sản phẩm được chọn
            if (productId) {
                const products = document.querySelectorAll('.product-item');
                products.forEach(product => {
                    // Lấy id sản phẩm từ id của phần tử li
                    const id = product.getAttribute('id').split('-')[1];
                    if (id !== productId) {
                        product.style.display = 'none';
                    }
                });
            }
        });
    </script>

    <script>
    // Hàm hiển thị giỏ hàng
    function displayCart() {
        // Lấy dữ liệu từ Local Storage hoặc cơ sở dữ liệu và hiển thị trong tbody
        // Cập nhật tổng số lượng và tổng giá tiền của sản phẩm
    }

    // Gọi hàm hiển thị giỏ hàng khi trang tải hoàn tất
    window.onload = function() {
        displayCart();
    };
    </script>
    <script>
    // Lưu trữ danh sách ban đầu của sản phẩm
    var initialProductList = $('.products').html();

    $(document).ready(function(){
        // Xử lý tìm kiếm
        $('#searchForm').submit(function(event) {
            event.preventDefault(); // Ngăn chặn form gửi đi

            var query = $('#searchForm input[name="query"]').val().toLowerCase(); // Lấy giá trị từ ô nhập liệu và chuyển thành chữ thường
            var $products = $('.products'); // Lấy danh sách sản phẩm

            // Khôi phục lại danh sách sản phẩm ban đầu
            $products.html(initialProductList);

            var foundResults = false; // Biến để kiểm tra xem có sản phẩm nào được tìm thấy không
            var isValidPrice = true; // Biến để kiểm tra giá trị tìm kiếm có hợp lệ hay không

            // Kiểm tra xem query có phải là một số hợp lệ trong phạm vi 1 đến 1000 hay không
            if (/^\d+$/.test(query)) {
                var price = parseInt(query, 10);
                if (price < 1 || price > 1000) {
                    isValidPrice = false;
                }
            }

            if (!isValidPrice) {
                $('#errorMessage').show();
                $('#noResultsMessage').hide();
                return;
            } else {
                $('#errorMessage').hide();
            }

            // Xóa tất cả các sản phẩm không liên quan và chỉ hiển thị các sản phẩm tìm kiếm
            $products.find('.product-item').each(function() {
                var $productName = $(this).find('.product-name');
                var productName = $productName.text().toLowerCase(); // Lấy tên sản phẩm và chuyển thành chữ thường
                var productPrice = parseFloat($(this).find('.product-price').text().replace(/\D/g, '')); // Lấy giá sản phẩm và chuyển thành số
                
                if (productName.includes(query) || productPrice.toString().includes(query)) {
                    foundResults = true; // Đặt biến này thành true nếu tìm thấy ít nhất một sản phẩm
                    
                    // Tô đậm từ khóa tìm kiếm trong tên sản phẩm
                    var regex = new RegExp(`(${query})`, 'gi');
                    var highlightedName = $productName.text().replace(regex, '<strong>$1</strong>');
                    $productName.html(highlightedName);
                } else {
                    $(this).closest('.product-list-item').remove();
                }
            });

            // Cuộn xuống sản phẩm đầu tiên tìm thấy
            if (foundResults) {
                $('html, body').animate({
                    scrollTop: $products.find('.product-item:visible').first().offset().top
                }, 500);
                $('#noResultsMessage').hide();
            } else {
                $('#noResultsMessage').show();
            }
        });
    });
    </script>

</body>
</html>
