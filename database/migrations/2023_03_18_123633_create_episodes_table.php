<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');

            $table->string('title');
            $table->string('slug');
            $table->string('type');
            $table->text('description');
            $table->text('body');
            $table->string('video');
            $table->string('tags');
            $table->string('time')->default('00:00:00');
            $table->integer('number');
            $table->boolean('status')->default(1);
            $table->integer('viewCount')->default(0);
            $table->integer('commentCount')->default(0);
            $table->integer('likeCount')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
