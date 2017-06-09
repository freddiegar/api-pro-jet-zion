<?php

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
            $table->string('api_token')->nullable()->index();
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_ip_address')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

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
