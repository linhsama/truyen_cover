<?php
require_once "../../../model/ChapterModel.php";
require_once "../../../model/ChapterNoiDungModel.php";
require_once "../../../model/CommonModel.php";
$chapter = new ChapterModel();
$chapterNoiDung = new ChapterNoiDungModel();
$cm = new CommonModel();

$defaultImagePath = "../../../assets/images/cover.png";

if (isset($_GET["req"])) {
    switch ($_GET["req"]) {
        case "add":
            $chapter_ten = $_POST["chapter_ten"];
            $chapter_ngay_cap_nhat = date("Y-m-d H:i:s");
            $chapter_trang_thai = $_POST["chapter_trang_thai"];
            $truyen_id = $_POST["truyen_id"];

            $chapter_id = $chapter->Chapter__Add($chapter_ten, $chapter_ngay_cap_nhat, $chapter_trang_thai, $truyen_id);

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
                
            if ($totalRes > 0 && $chapter_id > 0) {
                header("location: ../../index.php?pages=chapter&truyen_id=$truyen_id&msg=success");
                exit();
            } else {
                header("location: ../../index.php?pages=chapter&truyen_id=$truyen_id&msg=error");
                exit();
            }
            break;


        case "update":
            $res = 0;
            $chapter_id = $_POST["chapter_id"];
            $chapter_ten = $_POST["chapter_ten"];
            $chapter_ngay_cap_nhat = $_POST["chapter_ngay_cap_nhat"];
            $chapter_trang_thai = $_POST["chapter_trang_thai"];
            $truyen_id = $_POST["truyen_id"];
            $res += $chapter->Chapter__Update($chapter_id, $chapter_ten, $chapter_ngay_cap_nhat, $chapter_trang_thai, $truyen_id);
            if ($res != 0) {
                header("location: ../../index.php?pages=chapter&truyen_id=$truyen_id&msg=success");
            } else {
                header("location: ../../index.php?pages=chapter&truyen_id=$truyen_id&msg=error");
            }
            break;

        case "delete":
            $res = 0;
            $chapter_id = $_GET["chapter_id"];
            $truyen_id = $chapter->Chapter__Get_By_Id($chapter_id)->truyen_id;
            $res += $chapter->Chapter__Delete($chapter_id);
            if ($res != 0) {
                header("location: ../../index.php?pages=chapter&truyen_id=$truyen_id&msg=success");
            } else {
                header("location: ../../index.php?pages=chapter&truyen_id=$truyen_id&msg=error");
            }
            break;
        default:
            break;
    }
}