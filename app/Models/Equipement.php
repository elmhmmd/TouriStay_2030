<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    protected $fillable = ['name'];

    public function annonces()
    {
        return $this->belongsToMany(Annonce::class, 'annonce_equipement');
    }
}