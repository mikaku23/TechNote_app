<?php
// database/migrations/2026_01_06_add_notif_terkirim_to_penginstalans_and_perbaikans.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('penginstalans', function (Blueprint $table) {
            $table->boolean('notif_terkirim')->default(false)->after('estimasi');
        });

        Schema::table('perbaikans', function (Blueprint $table) {
            $table->boolean('notif_terkirim')->default(false)->after('estimasi');
        });
    }

    public function down()
    {
        Schema::table('penginstalans', function (Blueprint $table) {
            $table->dropColumn('notif_terkirim');
        });
        Schema::table('perbaikans', function (Blueprint $table) {
            $table->dropColumn('notif_terkirim');
        });
    }
};
