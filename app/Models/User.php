<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class User extends Model {
  use HasFactory;
  use SoftDeletes;

  public $timestamps = true;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['title', 'avatar','email', 'password'];
  protected $hidden = ['password'];

  public function tasks(): BelongsToMany
  {
      return $this->belongsToMany(Task::class);
  }

  public function setAvatarAttribute(UploadedFile $file = null)
  {
      // Delete any existing avatar file
      $this->deleteExistingAvatarFile();

      // If the file is empty, delete any current avatar
      if (is_null($file)) {
          $this->avatar_filename = null;
          $this->save();
          return;
      }

      // Store the file and update the record
      $uniqueFilename = date("U").rand(1000,100000) . '.' . $file->extension();
      $file->storePubliclyAs(
          'public/avatars',
          $uniqueFilename
      );
      $this->attributes['avatar_filename'] = $uniqueFilename;
  }

  public function getAvatarAttribute()
  {
      return $this->avatar_filename;
  }

  private function deleteExistingAvatarFile()
  {
      if (empty($this->avatar_filename)) {
          return;
      }

      Storage::delete('public/avatars/' . $this->avatar_filename);
  }

}
