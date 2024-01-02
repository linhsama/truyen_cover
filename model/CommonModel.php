<?php
class CommonModel
{
    function formatThousand($number)
    {
        $number = (int) preg_replace('/[^0-9]/', '', $number);

        if ($number >= 1000) {
            $roundedNumber = round($number);
            $formattedNumber = number_format($roundedNumber);
            $numberParts = explode(',', $formattedNumber);
            $magnitudeSuffix = array('K', 'M', 'B', 'T', 'Q');
            $countParts = count($numberParts) - 1;
            $formattedResult = $numberParts[0] . ((int) $numberParts[1][0] !== 0 ? '.' . $numberParts[1][0] : '');
            $formattedResult .= $magnitudeSuffix[$countParts - 1];

            return $formattedResult;
        }

        return $number;
    }

    function getTimeAgo($time)
    {
        if (!is_numeric($time)) {
           $time = strtotime($time);
        }

        $timeDifference = time() - $time;

        if ($timeDifference < 1) {
            return 'Less than 1 second ago';
        }

        $timeUnits = array(
            12 * 30 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60      => 'month',
            24 * 60 * 60           => 'day',
            60 * 60                => 'hour',
            60                     => 'minute',
            1                      => 'second'
        );

        foreach ($timeUnits as $seconds => $unit) {
            $division = $timeDifference / $seconds;

            if ($division >= 1) {
                $roundedValue = round($division);
                return $roundedValue . ' ' . $unit . (($roundedValue > 1) ? 's' : '') . ' ago';
            }
        }
    }

   
    // Hàm xử lý và kiểm tra tệp đã tải lên
    function processAndValidateUploadedFile($tempFilePath) {
        // Kiểm tra xem tệp có tồn tại không
        if (file_exists($tempFilePath)) {
            // Kiểm tra kích thước của tệp, không xử lý nếu dung lượng vượt quá 200MB
            if (filesize($tempFilePath) > 200 * 1024 * 1024) {
                echo "Cảnh báo: Dung lượng tệp vượt quá 200MB. Sử dụng hình ảnh mặc định.";
                return false;
            }
            // Đọc nội dung của tệp và trả về
            $imageContent = file_get_contents($tempFilePath);
            return $imageContent !== false ? $imageContent : false;
        } else {
            // Hiển thị thông báo lỗi nếu tệp không tồn tại và trả về false
            echo "Lỗi: Tệp không tồn tại.";
            return false;
        }
    }

}