<?php

namespace App\Transformers;

use App\Models\Module;
use League\Fractal\TransformerAbstract;

class ModuleTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Module $module)
    {
        return [
            'id' => $module->id,
            'name' => $module->name,
            'group_id' => $module->group_id,
        ];
    }

    public function includePermissions(Module $module)
    {
        $permissions = $module->permissions;

        return $this->collection($permissions, new PermissionTransformer(), 'skip');
    }
}
