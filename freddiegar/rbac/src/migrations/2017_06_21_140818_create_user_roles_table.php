<?php

use FreddieGar\Base\Constants\BlameColumn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateRolePermissionsTable
 */
class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');

            $table->unsignedInteger(BlameColumn::CREATED_BY)->nullable();
            $table->unsignedInteger(BlameColumn::UPDATED_BY)->nullable();
            $table->unsignedInteger(BlameColumn::DELETED_BY)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles');
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
        Schema::dropIfExists('user_roles');
    }
}
