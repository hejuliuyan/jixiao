<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
   protected $table = 'logs';

   protected $primaryKey = 'logs_id';

   public $timestamps = false;

}