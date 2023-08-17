<?php
  
namespace App\Enums;
 
enum NotificationTypeEnum:string {
    case Customer = 'customer';
    case Sales = 'sales';
    case Item = 'item';
    case Stock = 'stock';
}