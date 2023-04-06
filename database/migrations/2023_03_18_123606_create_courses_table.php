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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('skill_id');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');

            $table->string('title');
            $table->string('slug');
            $table->string('type');
            $table->text('description');
            $table->text('body');
            $table->string('image');
            $table->string('tags');
            $table->string('timeCourse');
            
            $table->integer('viewCount')->default(0);
            $table->integer('commentCount')->default(0);
            $table->integer('likeCount')->default(0);

            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->tinyInteger('status')->default(1);

            $table->unsignedInteger('sale_price')->default(0);
            $table->timestamp('date_on_sale_from')->nullable();
            $table->timestamp('date_on_sale_to')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
