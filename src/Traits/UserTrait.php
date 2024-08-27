<?php

namespace OtimOtim\PesapalIntegrationPackage\Traits;
use OtimOtim\PesapalIntegrationPackage\Models\PesapalTransaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;
trait UserTrait {

    public function pesapalTransactions() : MorphMany
    {
        return $this->MorphMany(PesapalTransaction::class, 'usable');
    }

   

}