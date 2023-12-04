<?php

namespace App\Transformers;

use App\Models\Role;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'is_disabled' => $role->is_disabled
        ];
    }

    public function includeModules(Role $role)
    {
        $modules = $role->modules()->get();

        return $this->collection($modules, new ModuleTransformer(), 'skip');
    }
}
