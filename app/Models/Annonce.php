<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    protected $table = 'annonces';

    protected $fillable = [
        'user_id', 'type_de_logement_id', 'location', 'price', 'image', 'available_until',
    ];
    protected $casts = [
        'available_until' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function typeDeLogement()
    {
        return $this->belongsTo(TypeDeLogement::class);
    }

    public function equipements()
    {
        return $this->belongsToMany(Equipement::class, 'annonce_equipement');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}