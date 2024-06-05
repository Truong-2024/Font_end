<?php
// Kiểm tra xem session có tồn tại không và hiển thị các giá trị từ session
$fullname = isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : "";
$email = isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : "";
$sdt = isset($_SESSION['sdt']) ? htmlspecialchars($_SESSION['sdt']) : "";
$profile_image = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : "../Image/LOGO/truong.jpg";
$email = $_SESSION['email'];

?>
<div class="account-right">
    <div class="title-account-right">
        <h3>Thông tin chi tiết</h3>
    </div>

    <div class="content-account">
        <div class="main-account">
            <div class="details">
                <div class="text">
                    <label class="font" for="">Tên đăng nhập:</label>
                </div>
                <div class="text-font">
                    <label class="font-text" for=""><?php echo htmlspecialchars($username); ?></label>
                </div>
            </div>
            <div class="details">
                <div class="text">
                    <label class="font" for="">Tên hiển thị:</label>
                </div>
                <div class="text-font">
                    <label class="font-text" for=""><?php echo htmlspecialchars($fullname); ?></label>
                </div>
            </div>
            <div class="details">
                <div class="text">
                    <label class="font" for="">Email:</label>
                </div>
                <div class="text-font">
                    <label class="font-text" for=""><?php echo htmlspecialchars($email); ?></label>
                </div>
            </div>
            <div class="details">
                <div class="text">
                    <label class="font" for="">Số điện thoại:</label>
                </div>
                <div class="text-font">
                    <label class="font-text" for=""><?php echo htmlspecialchars($sdt); ?></label>
                </div>
            </div>
            <div class="details">
                <div class="text">
                    <label class="font" for="">Địa chỉ:</label>
                </div>
                <div class="text-font">
                    <label class="font-text" for=""><?php echo htmlspecialchars($diachi); ?></label>
                </div>
            </div>
            <div class="details">
                <div class="text">
                    <label class="font" for="">Giới tính:</label>
                </div>
                <div class="text-font">
                    <label class="font-text" for=""><?php echo htmlspecialchars($gender); ?></label>
                </div>
            </div>
            <div class="details">
            <div class="text">
                <label class="font" for="birthdate">Ngày tháng năm sinh:</label>
            </div>
            <div class="text-font">
                <label for="" class="font-text"><?php echo htmlspecialchars($birthdate); ?></label>
            </div>
            </div>
            <a href="edit_user.php">
                <button type="button" class="edit-btn">Sửa</button>
            </a>
        </div>

        <div class="upload-img">
                <div class="content-upload-img">
                    <div class="grid-content-upload-img">
                        <!-- Hiển thị hình ảnh đã chọn -->
                        <div class="img"><img id="selected-image" src="<?php echo htmlspecialchars($profile_image); ?>" alt=""></div>
                    </div>
                    <!-- Input file và nút chọn ảnh -->
                    <form id="image-upload-form" action="save_image.php" method="post" enctype="multipart/form-data">
    <!-- Input hidden chứa email của người dùng -->
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
    <input type="file" id="file-upload" name="profile_image" class="type-img" accept=".jpg, .jpeg, .png" style="display: none;">
    <button type="button" id="select-image" class="btn-img">Chọn ảnh</button>
</form>
                    <!-- Thông báo về dung lượng và định dạng của ảnh -->
                    <div class="quydinh" id="fileSizeMessage" style="display: none;">Dung lượng file vượt quá 1 MB</div>
                    <div class="quydinh" id="fileFormatMessage" style="display: none;">Định dạng không hợp lệ</div>
                </div>
            </div>
</div>
<script>
    document.getElementById('select-image').addEventListener('click', function() {
        document.getElementById('file-upload').click();
    });

    document.getElementById('file-upload').addEventListener('change', function(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var selectedImage = document.getElementById('selected-image');
            selectedImage.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);

        var fileSize = event.target.files[0].size; // kích thước file (bytes)
        var maxSize = 1048576; // 1 MB = 1048576 bytes

        if (fileSize > maxSize) {
            document.getElementById('fileSizeMessage').style.display = 'block';
            return; // Không tiếp tục nếu kích thước vượt quá
        } else {
            document.getElementById('fileSizeMessage').style.display = 'none';
        }

        // Kiểm tra định dạng ảnh
        var allowedFormats = ['jpg', 'jpeg', 'png'];
        var fileExtension = event.target.files[0].name.split('.').pop().toLowerCase();
        if (allowedFormats.indexOf(fileExtension) === -1) {
            document.getElementById('fileFormatMessage').style.display = 'block';
            return; // Không tiếp tục nếu định dạng không hợp lệ
        } else {
            document.getElementById('fileFormatMessage').style.display = 'none';
        }

        // Submit form
        document.getElementById('image-upload-form').submit();
    });
</script>
