<?php
require_once '../../../model/TruyenModel.php';
require_once '../../../model/TruyenTheLoaiModel.php';
require_once '../../../model/CommonModel.php';
$truyen = new TruyenModel();
$truyenTheLoai = new TruyenTheLoaiModel();
$cm = new CommonModel();
// Kích thước mới cho ảnh (4 x 6)
$newWidth = 400;
$newHeight = 600;

// Đường dẫn tệp ảnh mặc định
$defaultImagePath = "../../../assets/images/cover.png";
// Ngưỡng kích thước tệp cho phép (ví dụ: 500MB)
$maxFileSize = 0.5 * 1024 * 1024 * 1024; // 500MB in bytes

// Xử lý yêu cầu
if (isset($_GET['req'])) {
    switch ($_GET['req']) {
        case "add":
            $res = 0;
            $truyen_ten = $_POST['truyen_ten'];
            $truyen_tac_gia = $_POST['truyen_tac_gia'];
            $truyen_mo_ta = $_POST['truyen_mo_ta'];
            $truyen_tinh_trang = 1;
            $truyen_luot_xem = 0;
            $truyen_luot_thich = 0;
            $truyen_luot_theo_doi = 0;
            $truyen_ngay_dang = date('Y-m-d H:i:s');
            $truyen_trang_thai = $_POST['truyen_trang_thai'];

          
            // Kiểm tra xem có tệp ảnh đã tải lên không
            if (!empty($_FILES["truyen_anh_bia"]["name"])) {
                $tempFilePath = $_FILES["truyen_anh_bia"]["tmp_name"];

                // Xử lý và kiểm tra nội dung ảnh đã tải lên
                if ($processedImageContent = $cm->processAndValidateUploadedFile($tempFilePath)) {
                    // Chuyển đổi thành base64 và thêm thông tin vào cơ sở dữ liệu
                    $base64Image = "data:image/jpeg;base64," . base64_encode($processedImageContent);
                    $truyen_anh_bia = $base64Image;
                } else {
                    // Sử dụng hình ảnh mặc định nếu xử lý thất bại
                    $defaultImageContent = file_get_contents($defaultImagePath);
                    $base64Image = "data:image/jpeg;base64," . base64_encode($defaultImageContent);
                    $truyen_anh_bia = $base64Image;
                }
            }else{
                // Sử dụng hình ảnh mặc định nếu xử lý thất bại
                $defaultImageContent = file_get_contents($defaultImagePath);
                $base64Image = "data:image/jpeg;base64," . base64_encode($defaultImageContent);
                $truyen_anh_bia = $base64Image;
            }

            $res += $truyen->Truyen__Add($truyen_ten, $truyen_tac_gia, $truyen_mo_ta, $truyen_anh_bia, $truyen_tinh_trang, $truyen_luot_xem, $truyen_luot_thich, $truyen_luot_theo_doi, $truyen_ngay_dang, $truyen_trang_thai);
            if ($res != 0) {
                $the_loai_id = isset($_POST['the_loai_id']) ? $_POST['the_loai_id'] : [];

                if (!empty($the_loai_id)) {
                    foreach ($the_loai_id as $item) {
                        $result_the_loai = $truyenTheLoai->TruyenTheLoai__Add($res, $item);

                        if (!$result_the_loai) {
                            header('location: ../../index.php?pages=truyen&msg=error');
                            break;
                        }
                    }
                }
                header('location: ../../index.php?pages=truyen&msg=success');
            } else {
                header('location: ../../index.php?pages=truyen&msg=error');
            }
            break;

        case "update":
            $res = 0;
            $truyen_id = $_POST['truyen_id'];
            $truyen_ten = $_POST['truyen_ten'];
            $truyen_tac_gia = $_POST['truyen_tac_gia'];
            $truyen_mo_ta = $_POST['truyen_mo_ta'];
            $truyen_tinh_trang = $_POST['truyen_tinh_trang'];
            $truyen_luot_xem = $_POST['truyen_luot_xem'];
            $truyen_ngay_dang = $_POST['truyen_ngay_dang'];
            $truyen_trang_thai = $_POST['truyen_trang_thai'];

              // Kiểm tra xem có tệp ảnh đã tải lên không
            if (!empty($_FILES["truyen_anh_bia"]["name"])) {
                $tempFilePath = $_FILES["truyen_anh_bia"]["tmp_name"];

                // Xử lý và kiểm tra nội dung ảnh đã tải lên
                if ($processedImageContent = $cm->processAndValidateUploadedFile($tempFilePath)) {
                    // Chuyển đổi thành base64 và thêm thông tin vào cơ sở dữ liệu
                    $base64Image = "data:image/jpeg;base64," . base64_encode($processedImageContent);
                    $truyen_anh_bia = $base64Image;
                } else {
                    $truyen_anh_bia =  $_POST['truyen_anh_bia_cu'];
                }
            }else{
                $truyen_anh_bia =  $_POST['truyen_anh_bia_cu'];
            }

            $the_loai_id = isset($_POST['the_loai_id']) ? $_POST['the_loai_id'] : [];

            if (!empty($the_loai_id)) {
                $res += $truyenTheLoai->TruyenTheLoai__Delete($truyen_id);

                foreach ($the_loai_id as $item) {
                    $res += $truyenTheLoai->TruyenTheLoai__Add($truyen_id, $item);
                }
            }


            $res += $truyen->Truyen__Update($truyen_id, $truyen_ten, $truyen_tac_gia, $truyen_mo_ta, $truyen_anh_bia, $truyen_tinh_trang, $truyen_trang_thai);
            if ($res != 0) {
                header('location: ../../index.php?pages=truyen&msg=success');
            } else {
                header('location: ../../index.php?pages=truyen&msg=error');
            }
            break;

        case "delete":
            $res = 0;
            $truyen_id = $_GET['truyen_id'];
            $res += $truyen->Truyen__Delete($truyen_id);
            if ($res != 0) {
                header('location: ../../index.php?pages=truyen&msg=success');
            } else {
                header('location: ../../index.php?pages=truyen&msg=error');
            }
            break;

        default:
            break;
    }
}