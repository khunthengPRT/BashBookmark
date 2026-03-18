<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('snippets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // SQLite does not support fulltext indexes; search falls back to LIKE.
            // MySQL and PostgreSQL both support fullText() in Laravel 9+.
            if (DB::getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'body', 'description']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snippets');
    }
};
