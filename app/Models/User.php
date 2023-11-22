<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    use Notifiable;

    public function routeNotificationFor($driver)
    {
        switch ($driver) {
            case 'mail':
                return $this->email;
            case 'database':
                return ['id' => $this->id];
            default:
                return null;
        }
    }
}
