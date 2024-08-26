<?php

namespace OtimOtim\PesapalIntegrationPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesapalTransactions extends Model
{
  use HasFactory;

  // Disable Laravel's mass assignment protection
  protected $guarded = []; 
  
}