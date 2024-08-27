<?php

namespace OtimOtim\PesapalIntegrationPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PesapalTransaction extends Model
{
  use HasFactory;

  // Disable Laravel's mass assignment protection
  protected $guarded = []; 
  

  public function usable(): MorphTo{
    return $this->morphTo();
}

public function modelable(): MorphTo{
    return $this->morphTo();
}
}