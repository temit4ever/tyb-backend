<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sprint extends Model {
  use HasFactory;
  use SoftDeletes;

  public $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];

  public $timestamps = true;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['title', 'start_date', 'end_date'];
}
