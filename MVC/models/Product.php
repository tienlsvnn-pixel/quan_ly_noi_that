<?php

class Product
{
    private $products = [
        [
            "id" => 1,
            "name" => "Ghế sofa",
            "price" => 5500000,
            "category" => "Phòng khách"
        ],
        [
            "id" => 2,
            "name" => "Bàn ăn gỗ",
            "price" => 3200000,
            "category" => "Phòng bếp"
        ],
        [
            "id" => 3,
            "name" => "Tủ quần áo",
            "price" => 4700000,
            "category" => "Phòng ngủ"
        ],
        [
            "id" => 4,
            "name" => "Kệ TV",
            "price" => 2100000,
            "category" => "Phòng khách"
        ]
    ];

    public function getAll()
    {
        return $this->products;
    }

    public function getById($id)
    {
        foreach ($this->products as $product) {
            if ($product["id"] == $id) {
                return $product;
            }
        }
        return null;
    }
}