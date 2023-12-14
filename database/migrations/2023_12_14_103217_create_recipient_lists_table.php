<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('recipient_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('recipients');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipient_lists');
    }
};
