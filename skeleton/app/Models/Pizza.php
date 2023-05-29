<?php

use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'street_address',
        'street_address_2',
        'city',
        'postcode',
        'state',
        'iso_country',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
}
