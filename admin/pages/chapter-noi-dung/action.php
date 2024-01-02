<?php
require_once "../../../model/ChapterNoiDungModel.php";
require_once "../../../model/CommonModel.php";
$chapterNoiDung = new ChapterNoiDungModel();
$cm = new CommonModel();

// Đường dẫn tệp ảnh mặc định
$defaultImagePath = "../../../assets/images/cover.png";
// Ngưỡng kích thước tệp cho phép (ví dụ: 500MB)
$maxFileSize = 0.5 * 1024 * 1024 * 1024; // 500MB in bytes

if (isset($_GET["req"])) {
    switch ($_GET["req"]) {
        case "add":
            $chapter_id = $_POST["chapter_id"];

            if (!empty($_FILES["chapter_noi_dung_image"]["name"][0])) {
                $uploadedImages = [];  // Tên biến thay đổi từ $uploadedFiles
                $totalRes = 0;

                // Duyệt qua danh sách các tệp đã tải lên
                foreach ($_FILES["chapter_noi_dung_image"]["name"] as $key => $uploadedFileName) {  // Tên biến $filename đã được đổi thành $uploadedFileName
                    $tempFilePath = $_FILES["chapter_noi_dung_image"]["tmp_name"][$key];

                    // Xử lý và kiểm tra nội dung ảnh đã tải lên
                    if ($processedImageContent = $cm->processAndValidateUploadedFile($tempFilePath)) {
                        // Chuyển đổi thành base64 và thêm thông tin vào cơ sở dữ liệu
                        $base64Image = "data:image/jpeg;base64," . base64_encode($processedImageContent);
                        $totalRes += $chapterNoiDung->ChapterNoiDung__Add($base64Image, $chapter_id);
                    } else {
                        // Sử dụng hình ảnh mặc định nếu xử lý thất bại
                        $defaultImageContent = file_get_contents($defaultImagePath);
                        $base64Image = "data:image/jpeg;base64," . base64_encode($defaultImageContent);
                        $totalRes += $chapterNoiDung->ChapterNoiDung__Add($base64Image, $chapter_id);
                    }
                }
            }
            
            if ($totalRes > 0) {
                header("Location: ../../index.php?pages=chapter-noi-dung&chapter_id=$chapter_id&msg=success");
                exit();
            } else {
                header("Location: ../../index.php?pages=chapter-noi-dung&chapter_id=$chapter_id&msg=error");
                exit();
            }


        case "update":
            $res = 0;
            $chapter_noi_dung_id = $_POST["chapter_noi_dung_id"];
            $chapter_id = $_POST["chapter_id"];

            // Kiểm tra xem có tệp ảnh đã tải lên không
            if (!empty($_FILES["chapter_noi_dung_image"]["name"])) {
                $tempFilePath = $_FILES["chapter_noi_dung_image"]["tmp_name"];

                // Xử lý và kiểm tra nội dung ảnh đã tải lên
                if ($processedImageContent = $cm->processAndValidateUploadedFile($tempFilePath)) {
                    // Chuyển đổi thành base64 và thêm thông tin vào cơ sở dữ liệu
                    $base64Image = "data:image/jpeg;base64," . base64_encode($processedImageContent);
                    $chapter_noi_dung_image = $base64Image;
                } else {
                    $chapter_noi_dung_image = $_POST["chapter_noi_dung_image_cu"];
                }
            }else{
                $chapter_noi_dung_image = $_POST["chapter_noi_dung_image_cu"];
            }
            $res += $chapterNoiDung->ChapterNoiDung__Update($chapter_noi_dung_id, $chapter_noi_dung_image, $chapter_id);
            
            if ($res != 0) {
                header("location: ../../index.php?pages=chapter-noi-dung&chapter_id=$chapter_id&msg=success");
            } else {
                header("location: ../../index.php?pages=chapter-noi-dung&chapter_id=$chapter_id&msg=error");
            }
            break;

        case "delete":
            $res = 0;
            $chapter_noi_dung_id = $_GET["chapter_noi_dung_id"];
            $chapter_id = $chapterNoiDung->ChapterNoiDung__Get_By_Id($chapter_noi_dung_id)->chapter_id;
            $res += $chapterNoiDung->ChapterNoiDung__Delete($chapter_noi_dung_id);
            if ($res != 0) {
                header("location: ../../index.php?pages=chapter-noi-dung&chapter_id=$chapter_id&msg=success");
            } else {
                header("location: ../../index.php?pages=chapter-noi-dung&chapter_id=$chapter_id&msg=error");
            }
            break;

        default:
            break;
    }
}