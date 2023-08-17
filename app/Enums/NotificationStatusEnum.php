<?php
  
namespace App\Enums;
 
enum NotificationStatusEnum:string {
    case Read = 'read';
    case Unread = 'unread';
}