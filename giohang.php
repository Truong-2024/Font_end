<?php
session_start();

// Kiểm tra nếu giỏ hàng không tồn tại hoặc rỗng, hiển thị thông báo và kết thúc script
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Giỏ hàng của bạn hiện đang trống.";
    exit();
}

// Lưu thông tin sản phẩm vào biến session
$_SESSION['selected_products'] = array();

foreach ($_SESSION['cart'] as $product) {
    // Tạo key duy nhất cho sản phẩm dựa trên mã sản phẩm và size
    $key = $product['MaSanPham'] . '_' . $product['Size'];
    // Kiểm tra xem sản phẩm đã tồn tại trong session chưa
    if (isset($_SESSION['selected_products'][$key])) {
        // Nếu sản phẩm đã tồn tại, cập nhật số lượng
        $_SESSION['selected_products'][$key]['SoLuong'] += $product['SoLuong'];
    } else {
        // Nếu sản phẩm chưa tồn tại, thêm mới vào session
        $_SESSION['selected_products'][$key] = $product;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="../CSS/giohang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        const SERVER_URL = 'remove_from_cart.php';
        // Lưu thông tin giỏ hàng vào localStorage
        function saveCartToLocalStorage(cart) {
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        // Lấy thông tin giỏ hàng từ localStorage
        function getCartFromLocalStorage() {
            const cart = localStorage.getItem('cart');
            return cart ? JSON.parse(cart) : [];
        }

        // Khi trang được tải, kiểm tra xem có thông tin giỏ hàng nào được lưu trữ không
        document.addEventListener('DOMContentLoaded', function() {
            const cart = getCartFromLocalStorage();
            // Hiển thị thông tin giỏ hàng lên trang
            
        });

        // Khi người dùng thêm sản phẩm vào giỏ hàng
        function addToCart(productId) {
            // Xử lý logic thêm sản phẩm vào giỏ hàng
            // ...
            // Sau khi thêm, lưu thông tin giỏ hàng mới vào localStorage
            saveCartToLocalStorage(cart);
        }
        function updateSummary() {
    let totalQuantity = 0;
    let totalPrice = 0;

    document.querySelectorAll("#cart-table tbody tr").forEach(row => {
        const checkbox = row.querySelector("input[type='checkbox']");
        if (checkbox && checkbox.checked) {
            const quantityElement = row.querySelector(".quantity");
            const quantity = quantityElement ? parseInt(quantityElement.innerText) : 0;
            const priceElement = row.querySelector(".price");
            const price = priceElement ? parseFloat(priceElement.innerText.replace("đ", "").replace(/\./g, "").replace(",", ".")) : 0;
            totalQuantity += quantity;
            totalPrice += quantity * price;
        }
    });

    document.getElementById("total-quantity").innerText = totalQuantity;
    document.getElementById("total-price").innerText = formatCurrency(totalPrice);
    document.getElementById("final-price").innerText = formatCurrency(totalPrice);
}

        function formatCurrency(number) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number);
        }

        function removeFromCart(maSanPham) {
            fetch(SERVER_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'MaSanPham=' + encodeURIComponent(maSanPham),
            })
            .then(response => response.text())
            .then(result => {
                if (result.trim() === 'success') {
                    alert('Sản phẩm đã được xóa khỏi giỏ hàng');
                    location.reload();
                } else {
                    alert('Đã có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function updateQuantity(maSanPham, size, change) {
    const row = document.querySelector(`#product-${maSanPham}-${size}`);
    if (!row) {
        console.log("Row not found for maSanPham:", maSanPham);
        return;
    }
    const quantityElement = row.querySelector(".quantity");
    let quantity = parseInt(quantityElement.innerText) + change;

    if (quantity < 1) quantity = 1;

    // Cập nhật số lượng trên giao diện
    quantityElement.innerText = quantity;

    const priceElement = row.querySelector(".price");
    const price = priceElement ? parseFloat(priceElement.innerText.replace("đ", "").replace(/\./g, "").replace(",", ".")) : 0;
    const totalElement = row.querySelector(".total-price");
    const total = price * quantity;
    totalElement.innerText = formatCurrency(total);

    // Lấy thông tin giỏ hàng từ localStorage
    const cart = getCartFromLocalStorage();

    // Tạo key duy nhất cho sản phẩm dựa trên mã sản phẩm và size
    const key = maSanPham + '_' + size;

    // Kiểm tra xem đối tượng cart và thuộc tính SoLuong đã được định nghĩa hay chưa
    if (!cart.hasOwnProperty(key)) {
        cart[key] = {}; // Nếu chưa, khởi tạo thuộc tính SoLuong
    }

    // Gán giá trị cho thuộc tính SoLuong
    cart[key].SoLuong = quantity;

    // Lưu thông tin giỏ hàng vào localStorage
    saveCartToLocalStorage(cart);

    // Gửi yêu cầu AJAX để cập nhật số lượng trong session
    fetch('update_cart_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'MaSanPham=' + encodeURIComponent(maSanPham) + '&Size=' + encodeURIComponent(size) + '&SoLuong=' + encodeURIComponent(quantity),
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === 'success') {
            updateSummary();
        } else {
            console.error('Error updating quantity:', result);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


        function updateCartIconQuantity() {
            let totalQuantity = <?php echo count($_SESSION['cart']); ?>;
            document.getElementById("total-quantity-display").innerText = totalQuantity;
        }

        document.addEventListener("DOMContentLoaded", function() {
    const selectAllCheckbox = document.getElementById("select-all");
    const productCheckboxes = document.querySelectorAll("#cart-table tbody input[type='checkbox']");

    // Sự kiện khi checkbox "Chọn tất cả" được thay đổi
    selectAllCheckbox.addEventListener('change', function() {
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateSummary();
    });

    // Sự kiện khi checkbox của từng sản phẩm được thay đổi
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!checkbox.checked) {
                selectAllCheckbox.checked = false;
            } else if (Array.from(productCheckboxes).every(cb => cb.checked)) {
                selectAllCheckbox.checked = true;
            }
            updateSummary();
        });
    });

    // Cập nhật biểu tượng giỏ hàng
    updateCartIconQuantity();
});


function addToCart(maSanPham, tenSanPham, gia, hinhAnh) {
    const productElement = event.target.closest('.product-item');

    // Tìm trường số lượng trong phần tử sản phẩm cha
    const quantityInput = productElement.querySelector('.quantity-input');

    // Lấy giá trị số lượng từ trường số lượng
    const soLuong = quantityInput.value;

    // Tìm phần tử chứa kích thước
    const sizeElement = productElement.querySelector('.psize.active');

    if (!sizeElement) {
        alert('Vui lòng chọn kích thước');
        return;
    }

    const size = sizeElement.textContent;

    const formData = new FormData();
    formData.append('MaSanPham', maSanPham);
    formData.append('TenSanPham', tenSanPham);
    formData.append('Gia', gia);
    formData.append('HinhAnh', hinhAnh);
    formData.append('SoLuong', soLuong);
    // Thêm dữ liệu kích thước đã chọn vào FormData
    formData.append('Size', size);

    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === 'success') {
            alert('Sản phẩm đã được thêm vào giỏ hàng');
            // Cập nhật biểu tượng giỏ hàng
            updateCartIconQuantity();
            // Thêm input ẩn vào form và submit để chuyển hướng người dùng đến trang thanh toán
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_product';
            input.value = maSanPham + '_' + size;
            document.getElementById('checkout-form').appendChild(input);
            document.getElementById('checkout-form').submit();
        } else {
            alert('Đã có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


function removeFromCart(key) {
    const [maSanPham, size] = key.split('_');
    fetch(SERVER_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'MaSanPham=' + encodeURIComponent(maSanPham) + '&Size=' + encodeURIComponent(size),
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === 'success') {
            alert('Sản phẩm đã được xóa khỏi giỏ hàng');
            location.reload();
        } else {
            alert('Đã có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
function updateQuantity(key, change) {
    const [maSanPham, size] = key.split('_');
    const row = document.querySelector(`#product-${maSanPham}-${size}`);
    if (!row) {
        console.log("Row not found for key:", key);
        return;
    }
    const quantityElement = row.querySelector(".quantity");
    let quantity = parseInt(quantityElement.innerText) + change;

    if (quantity < 1) quantity = 1;

    // Cập nhật số lượng trên giao diện
    quantityElement.innerText = quantity;

    const priceElement = row.querySelector(".price");
    const price = priceElement ? parseFloat(priceElement.innerText.replace("đ", "").replace(/\./g, "").replace(",", ".")) : 0;
    const totalElement = row.querySelector(".total-price");
    const total = price * quantity;
    totalElement.innerText = formatCurrency(total);

    // Lấy thông tin giỏ hàng từ localStorage
    const cart = getCartFromLocalStorage();

    // Kiểm tra xem đối tượng cart và thuộc tính SoLuong đã được định nghĩa hay chưa
    if (!cart.hasOwnProperty(key)) {
        cart[key] = {}; // Nếu chưa, khởi tạo thuộc tính SoLuong
    }

    // Gán giá trị cho thuộc tính SoLuong
    cart[key].SoLuong = quantity;

    // Lưu thông tin giỏ hàng vào localStorage
    saveCartToLocalStorage(cart);

    // Gửi yêu cầu AJAX để cập nhật số lượng trong session
    fetch('update_cart_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'MaSanPham=' + encodeURIComponent(maSanPham) + '&Size=' + encodeURIComponent(size) + '&SoLuong=' + encodeURIComponent(quantity),
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === 'success') {
            updateSummary(); // Cập nhật tổng số lượng và giá trị tổng giá trong giỏ hàng
            updateCartIconQuantity(); // Cập nhật biểu tượng giỏ hàng
        } else {
            console.error('Error updating quantity:', result);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
document.addEventListener("DOMContentLoaded", function() {
    const selectAllCheckbox = document.getElementById("select-all");
    const productCheckboxes = document.querySelectorAll("#cart-table tbody input[type='checkbox']");

    // Sự kiện khi checkbox "Chọn tất cả" được thay đổi
    selectAllCheckbox.addEventListener('change', function() {
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateSummary();
    });

    // Sự kiện khi checkbox của từng sản phẩm được thay đổi
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!checkbox.checked) {
                selectAllCheckbox.checked = false;
            } else if (Array.from(productCheckboxes).every(cb => cb.checked)) {
                selectAllCheckbox.checked = true;
            }
            updateSummary();
        });
    });

    // Cập nhật biểu tượng giỏ hàng
    updateCartIconQuantity();
    document.getElementById('checkout-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn chặn việc submit mặc định của form

    const selectedProducts = [];
    document.querySelectorAll("#cart-table tbody tr").forEach(row => {
        const checkbox = row.querySelector("input[type='checkbox']");
        if (checkbox && checkbox.checked) {
            const productKey = checkbox.value;
            selectedProducts.push(productKey);
        }
    });

    // Tạo một input ẩn chứa danh sách các sản phẩm được chọn
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'selected_products';
    input.value = JSON.stringify(selectedProducts);

    // Thêm input vào form và submit
    this.appendChild(input);
    this.submit();
});

});

    </script>
</head>
<body>
    <div class="wrapper">
        <header>
            <nav class="container">
                <a href="" id="logo">
                    <img src="../Image/LOGO/Remove-bg.ai_1711328789070.png" alt="">
                </a>
                <ul id="main-menu">
                    <li><a href="nguoidung.php">Trang chủ</a></li>
                    <li><a href="">Sản phẩm</a>
                        <ul class="sub-menu">
                            <li><a href="danhmuc.php?category=DM1">Giày Tây</a></li>
                            <li><a href="danhmuc.php?category=DM4">Dép</a></li>
                        </ul>
                    </li>
                    <li><a href="">Hãng</a>
                        <ul class="sub-menu">
                            <li><a href="danhmuc.php?category=DM2">NIKE</a></li>
                            <li><a href="danhmuc.php?category=DM3">ADIDAS</a></li>
                        </ul>
                    </li>
                    <li><a href="gioithieu.php">Giới thiệu</a></li>
                    <li><a href="">Liên hệ</a></li>
                </ul>
                <form id="searchForm" action="#" method="GET">
                    <div class="search-container">
                        <input type="text" name="query" placeholder="Tìm kiếm...">
                        <button type="submit" class="pk" style="padding: 11px 11px; font-size: 12px;"><i class="fa-solid fa-magnifying-glass"></i></button>
                        <div id="noResultsMessage" style="display: none;">Không tìm thấy sản phẩm phù hợp.</div>
                    </div>
                </form>
                <div class="giohang-dangnhap-dangky">
                    <a href="../user/index.php"><button><i class="fa-solid fa-user"></i></button></a>
                    <a href="login.php"><button class="logout">Logout</button></a>
                </div>
                <div class="cart-icon">
                    <a href="giohang.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="total-quantity-display">0</span>
                    </a>
                </div>
            </nav>
        </header>
        <section class="cart">
            <div class="container">
                <div class="cart-content">
                    <div class="cart-content-left">
                        <form id="checkout-form" action="thanhtoan.php" method="POST">
                            <table id="cart-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Sản phẩm</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Size</th>
                                        <th>Số lượng</th>
                                        <th>Giá</th>
                                        <th>Thành tiền</th>
                                        <th>Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
            foreach ($_SESSION['selected_products'] as $key => $product) {
                $gia = floatval(str_replace(".", "", $product['Gia'])); // Chuyển giá thành số thực
                $soLuong = intval($product['SoLuong']); // Chuyển số lượng thành số nguyên
                $formattedPrice = number_format($gia, 0, '.', '.') . "đ"; // Định dạng giá
                $thanhTien = $soLuong * $gia; // Thực hiện phép nhân giữa số lượng và giá;
                $formattedTotalPrice = number_format($thanhTien, 0, '.', '.') . "đ";
            
                echo "<tr id='product-{$product['MaSanPham']}-{$product['Size']}'>";
                echo "<td><input type='checkbox' name='selected_products[]' value='{$key}'></td>";
                echo "<td><img src='{$product['HinhAnh']}' alt='{$product['TenSanPham']}' class='product-image'></td>";
                echo "<td>{$product['TenSanPham']}</td>";
                echo "<td>{$product['Size']}</td>";
                echo "<td class='quantity-container'>
                        <button type='button' onclick='updateQuantity(\"{$key}\", -1)'>-</button>
                        <span class='quantity'>{$product['SoLuong']}</span>
                        <button type='button' onclick='updateQuantity(\"{$key}\", 1)'>+</button>
                      </td>";
                echo "<td class='price'>{$formattedPrice}</td>";
                echo "<td class='total-price'>{$formattedTotalPrice}</td>"; // Thêm cột thành tiền ở đây
                echo "<td><button type='button' onclick='removeFromCart(\"{$key}\")'><i class='fas fa-times'></i></button></td>";
                echo "</tr>";
            }
        ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8">
                                            <label class="checkbox-label" for="select-all">
                                                <input type="checkbox" id="select-all">
                                                CHỌN TẤT CẢ
                                            </label>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>
                    </div>
                    <div class="cart-content-right">
                        <table>
                            <tr>
                                <th colspan="2">TỔNG TIỀN GIỎ HÀNG</th>
                            </tr>
                            <tr>
                                <td>TỔNG SẢN PHẨM</td>
                                <td id="total-quantity">0</td>
                            </tr>
                            <tr>
                                <td>TỔNG TIỀN HÀNG</td>
                                <td id="total-price">0</td>
                            </tr>
                            <tr>
                                <td>THÀNH TIỀN</td>
                                <td id="final-price">0</td>
                            </tr>
                        </table>
                        <div class="cart-content-right-button">
        <button type="submit" form="checkout-form">Thanh toán</button>
    </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <h4>Thông tin</h4>
                        <ul>
                            <li><a href="#">Về chúng tôi</a></li>
                            <li><a href="#">Quy định</a></li>
                            <li><a href="#">Chính sách bảo mật</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h4>Hỗ trợ</h4>
                        <ul>
                            <li><a href="#">Trung tâm trợ giúp</a></li>
                            <li><a href="#">Liên hệ</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h4>Theo dõi chúng tôi</h4>
                        <ul>
                            <li><a href="#">Facebook</a></li>
                            <li><a href="#">Instagram</a></li>
                            <li><a href="#">Twitter</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h4>Đăng ký nhận tin</h4>
                        <p>Nhận thông tin mới nhất về sản phẩm và khuyến mãi.</p>
                        <form action="#">
                            <input type="email" placeholder="Nhập email của bạn">
                            <button type="submit">Đăng ký</button>
                        </form>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
