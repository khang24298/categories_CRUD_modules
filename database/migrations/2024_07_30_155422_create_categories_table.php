<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CategoryType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->smallInteger('level')->comment('default level is root(1), child = parent level + 1');
            $table->string('type')->default(CategoryType::SERVICE);
            $table->string('key')->unique()->comment('Mostly used as slug, e.g. health-check');
            $table->string('code')->unique()->comment('Admin defined code, all uppercase usually shorthanded, e.g. HEA');
            $table->json('name');
            $table->boolean('active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
