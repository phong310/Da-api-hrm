<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

class AddColsIsDisabledToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_disabled')->after('guard_name')->default(false);
        });

        $roles = Config::get('default_permissions.roles_default');
        $data = Role::query()->get();

        foreach ($data as $d) {
            if (in_array($d->name, $roles)) {
                $d->is_disabled = true;
                $d->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('is_disabled');
        });
    }
}
