<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('notes', 'deleted_with_subject')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->boolean('deleted_with_subject')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notes', 'deleted_with_subject')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->dropColumn('deleted_with_subject');
            });
        }
    }
};
