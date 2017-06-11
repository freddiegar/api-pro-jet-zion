<?php

use App\Constants\BlameColumn;
use App\Constants\UserStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('status', [
                UserStatus::ACTIVE,
                UserStatus::INACTIVE,
                UserStatus::SUSPENDED,
                UserStatus::BLOCKED
            ])->default(UserStatus::ACTIVE);
            $table->string('username')->index();
            $table->string('password');
            $table->string('type')->index();
            $table->string('api_token')->nullable()->index()->unique();
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_ip_address')->nullable();
            $table->unsignedInteger(BlameColumn::CREATED_BY)->nullable();
            $table->unsignedInteger(BlameColumn::UPDATED_BY)->nullable();
            $table->unsignedInteger(BlameColumn::DELETED_BY)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(BlameColumn::CREATED_BY)->references('id')->on('users');
            $table->foreign(BlameColumn::UPDATED_BY)->references('id')->on('users');
            $table->foreign(BlameColumn::DELETED_BY)->references('id')->on('users');

            $table->unique(['username', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
