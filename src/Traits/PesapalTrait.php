<?php

namespace OtimOtim\PesapalIntegrationPackage\Traits;
use OtimOtim\PesapalIntegrationPackage\Models\PesapalTransaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;
trait PesapalTrait {

    public function pesapalTransactions() : MorphMany
    {
        return $this->MorphMany(PesapalTransaction::class, 'modelable');
    }

}