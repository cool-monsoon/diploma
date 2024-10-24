<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Show;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Show::all()->each(function (Show $show) {
            $date = Carbon::parse($show->date);
            $show->date = $date->format('Y-m-d');
            $show->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Show::all()->each(function (Show $show) {
            $date = Carbon::parse($show->date);
            $show->date = $date->format('Y-m-d H:i:s');
            $show->save();
        });
    }
};
