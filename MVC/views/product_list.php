<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bán nội thất</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        a {
            text-decoration: none;
            color: blue;
        }
    </style>
</head>
<body>
    <h1>Danh sách sản phẩm nội thất</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Loại</th>
            <th>Giá</th>
            <th>Chi tiết</th>
        </tr>

        <?php foreach ($products as $item): ?>
            <tr>
                <td><?php echo $item["id"]; ?></td>
                <td><?php echo $item["name"]; ?></td>
                <td><?php echo $item["category"]; ?></td>
                <td><?php echo number_format($item["price"], 0, ',', '.'); ?> VNĐ</td>
                <td>
                    <a href="index.php?action=detail&id=<?php echo $item["id"]; ?>">
                        Xem
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>