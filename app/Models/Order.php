<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillabe = [
        'price',
        'description',
        'status',
        'reference_number'
    ];
    public static function getProductPrice($value)
    {
        switch ($value) {
            case "product-1":
                $price = "100";
                break;
            case "product-2":
                $price = "200";
                break;
            case "product-3":
                $price = "300";
                break;
            default:
                $price = "0.0";
        }
        return $price;
    }
    public static function getProductDescription($value){
        switch ($value) {
            case "product-1":
                $description = "$100 product";
                break;
            case "product-2":
                $description = "$200 product";
                break;
            case "product-3":
                $description = "$300 product";
                break;
            default:
                $description = "0.0";
        }
        return $description;
    };
}
