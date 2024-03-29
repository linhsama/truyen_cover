<?php
require_once '../model/TruyenModel.php';
require_once '../model/TheLoaiModel.php';
require_once '../model/CommonModel.php';
require_once '../model/ChapterModel.php';
$truyen = new TruyenModel();
$theLoai = new TheLoaiModel();
$cm = new CommonModel();
$chapter = new ChapterModel();

if (!isset($_GET['tu-khoa'])) {
    $truyen__Get_Ten_Truyen_Paged = ["Không tìm thấy truyện này!"];
}
$tu_khoa = $_GET['tu-khoa'];
// Lấy số trang từ tham số truyền vào hoặc mặc định là 1
$page_number = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Lấy danh sách truyện cho trang hiện tại
$truyen__Get_Ten_Truyen_Paged = $truyen->Truyen__Get_Ten_Truyen_Paged($page_number, $tu_khoa);
?>

<main class="main">
    <div class="main-container">
        <div class="main-title-container">
            <a href="index.php?pages=truyen-tim-kiem">
                <div class="item-title color-2"><i class='bx bx-star bx-tada'></i>TRUYỆN TÌM KIẾM</div>
            </a>
        </div>
        <div class="main-item-container">
            <?php foreach ($truyen__Get_Ten_Truyen_Paged as $item) : ?>
                <?php if (count($truyen__Get_Ten_Truyen_Paged) > 0) : ?>
                    <a href="index.php?pages=truyen-chi-tiet&truyen_id=<?= $item->truyen_id ?>">
                        <div class="manga-container">
                            <div class="manga-thumbnail">
                                <img src="<?= $item->truyen_anh_bia ?>">
                                <span class="manga-note background-2"><?= $cm->formatThousand($item->truyen_luot_xem) ?> <i class="bx bxs-star"></i></span>
                            </div>
                            <div class="manga-title color-2"><?= $item->truyen_ten ?></div>
                        </div>
                    </a>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>

    <div class="pagination-container">
        <div class="pagination">
            <?php
            $total_pages = ceil(count($truyen__Get_Ten_Truyen_Paged) / 10);

            // Hiển thị nút đầu trang
            if ($page_number > 1) {
                echo '<a href="index.php?pages=truyen-tim-kiem&page=1" class="pagination-link">Đầu trang</a>';
            }

            // Hiển thị nút trước
            if ($page_number > 1) {
                echo '<a href="index.php?pages=truyen-tim-kiem&page=' . ($page_number - 1) . '" class="pagination-link">Trang trước</a>';
            }

            // Hiển thị các trang gần đó
            for ($i = max(1, $page_number - 2); $i <= min($page_number + 2, $total_pages); $i++) {
                echo '<a href="index.php?pages=truyen-tim-kiem&page=' . $i . '" class="pagination-link ' . ($page_number == $i ? 'active' : '') . '">' . $i . '</a>';
            }

            // Hiển thị nút sau
            if ($page_number < $total_pages) {
                echo '<a href="index.php?pages=truyen-tim-kiem&page=' . ($page_number + 1) . '" class="pagination-link">Trang sau</a>';
            }

            // Hiển thị nút cuối trang
            if ($page_number < $total_pages) {
                echo '<a href="index.php?pages=truyen-tim-kiem&page=' . $total_pages . '" class="pagination-link">Cuối trang</a>';
            }
            ?>
        </div>
    </div>

</main>