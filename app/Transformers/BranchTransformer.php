<?php

namespace App\Transformers;

use App\Models\Master\Branch;
use League\Fractal\TransformerAbstract;

class BranchTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Branch $branch)
    {

        return [
            'id' => $branch->id,
            'name' => $branch->name,
        ];
    }
}
