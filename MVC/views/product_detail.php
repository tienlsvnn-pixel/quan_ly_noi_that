<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        .box {
            border: 1px solid #ccc;
            padding: 20px;
            width: 400px;
        }
        a {
            text-decoration: none;
            color: blue;
        }
    </style>
</head>
<body>
    <h1>Chi tiết sản phẩm</h1>

    <?php if ($product): ?>
        <div class="box">
            <p><strong>ID:</strong> <?php echo $product["id"]; ?></p>
            <p><strong>Tên:</strong> <?php echo $product["name"]; ?></p>
            <p><strong>Loại:</strong> <?php echo $product["category"]; ?></p>
            <p><strong>Giá:</strong> <?php echo number_format($product["price"], 0, ',', '.'); ?> VNĐ</p>
        </div>
    <?php else: ?>
        <p>Không tìm thấy sản phẩm.</p>
    <?php endif; ?>

    <br>
    <a href="index.php">Quay lại danh sách</a>
</body>
</html>