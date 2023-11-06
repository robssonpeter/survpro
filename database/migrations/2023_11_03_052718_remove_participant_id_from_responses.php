<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveParticipantIdFromResponses extends Migration
{
    public function up()
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropForeign(['participant_id']);
            $table->dropColumn('participant_id');
        });
    }

    public function down()
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->unsignedBigInteger('participant_id');
            $table->foreign('participant_id')
                ->references('id')
                ->on('participants')
                ->onDelete('cascade');
        });
    }
}

