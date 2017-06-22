<?php

use FreddieGar\Base\Constants\BlameColumn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateRolePermissionsTable
 */
class CreateRolePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('permission_id')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->tinyInteger('granted')->default(1);

            $table->unsignedInteger(BlameColumn::CREATED_BY)->nullable();
            $table->unsignedInteger(BlameColumn::UPDATED_BY)->nullable();
            $table->unsignedInteger(BlameColumn::DELETED_BY)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->foreign('parent_id')->references('id')->on('roles');
            $table->foreign(BlameColumn::CREATED_BY)->references('id')->on('users');
            $table->foreign(BlameColumn::UPDATED_BY)->references('id')->on('users');
            $table->foreign(BlameColumn::DELETED_BY)->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_permissions');
    }
}
