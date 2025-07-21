<?php

namespace App\Console\Commands;

use App\Mail\NotificationEmail;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Notification;
use App\Models\Pompa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckUnprosessedPompa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-unprosessed-pompa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $unprosessed_usulan = DB::table('pompa')
            ->where('pompa.status_id', '=', 1)
            // ->where('pompa.created_at', '>=', Date::now()->subDay(5))
            ->where('pompa.created_at', '<', Date::now()->subDay(7))
            ->join('poktan', 'poktan.id', '=', 'pompa.poktan_id')
            ->join('desa', 'desa.id', '=', 'pompa.desa_id')
            ->join('kecamatan', 'kecamatan.id', '=', 'desa.kecamatan_id')
            ->join('kabupaten', 'kabupaten.id', '=', 'kecamatan.kabupaten_id')
            ->join('users', 'users.id', '=', 'kabupaten.pj_id')
            ->select('pompa.id', 'pompa.created_at', 'pompa.updated_at', 'kabupaten.pj_id as kabupaten_pj_id', 'poktan.name as poktan_name', 'desa.name as desa_name', 'kecamatan.name as kecamatan_name', 'users.email as kabupaten_pj_email')
            ->orderBy('pompa.created_at')
            ->get()
            ->groupBy('kabupaten_pj_id');
        if (count($unprosessed_usulan)) {
            foreach ($unprosessed_usulan as $key => $usulan) {
                $notif_usulan_data = [
                    'receiver_id' => $key,
                    'subject' => 'Proses Usulan',
                    'title' => count($usulan).' Usulan Pompa Belum Diproses',
                    'message' => 'Dalam 7 hari '.count($usulan).' usulan pompa belum diproses. Mohon segera proses usulan yang diajukan.'
                ];
                $notif_usulan = Notification::create($notif_usulan_data);
                $links = [];
                foreach ($usulan as $us) {
                    $links[] = [
                        'name' => 'lihat usulan belum diproses kelompok tani '.$us->poktan_name.' kecamatan '.$us->kecamatan_name.' desa '.$us->desa_name,
                        'url' => route('kabupaten.usulan', ['src' => Crypt::encryptString($us->id)])
                    ];
                }
                if (count($links)) $notif_usulan->links()->createMany($links);
                Mail::to($usulan[0]->kabupaten_pj_email)->send(new NotificationEmail([...$notif_usulan_data, 'links' => $links]));
            }
        }
        $unprosessed_dimanfaatkan = DB::table('pompa')
            ->where('pompa.status_id', '=', 3)
            ->whereColumn('pompa.dimanfaatkan_unit', '=', 'pompa.diterima_unit')
            ->where('pompa.updated_at', '<', Date::now()->subDay(7))
            ->join('poktan', 'poktan.id', '=', 'pompa.poktan_id')
            ->join('desa', 'desa.id', '=', 'pompa.desa_id')
            ->join('kecamatan', 'kecamatan.id', '=', 'desa.kecamatan_id')
            ->join('kabupaten', 'kabupaten.id', '=', 'kecamatan.kabupaten_id')
            ->join('users', 'users.id', '=', 'kabupaten.pj_id')
            ->select('pompa.id', 'pompa.created_at', 'pompa.updated_at', 'kabupaten.pj_id as kabupaten_pj_id', 'poktan.name as poktan_name', 'desa.name as desa_name', 'kecamatan.name as kecamatan_name', 'users.email as kabupaten_pj_email')
            ->orderBy('pompa.updated_at')
            ->get()
            ->groupBy('kabupaten_pj_id');
        if (count($unprosessed_dimanfaatkan)) {
            foreach ($unprosessed_dimanfaatkan as $key => $dimanfaatkan) {
                $notif_dimanfaatkan_data = [
                    'receiver_id' => $key,
                    'subject' => 'Proses Pemanfaatan',
                    'title' => count($dimanfaatkan).' Pemanfaatan Pompa Belum Diproses',
                    'message' => 'Dalam 7 hari '.count($dimanfaatkan).' pemanfaatan pompa belum diproses. Mohon segera proses usulan yang diajukan.'
                ];
                $notif_dimanfaatkan = Notification::create($notif_dimanfaatkan_data);
                $links = [];
                foreach ($dimanfaatkan as $dm) {
                    $links[] = [
                        'name' => 'lihat pemanfaatan belum diproses kelompok tani '.$dm->poktan_name.' kecamatan '.$dm->kecamatan_name.' desa '.$dm->desa_name,
                        'url' => route('kabupaten.dimanfaatkan', ['src' => Crypt::encryptString($dm->id)])
                    ];
                }
                if (count($links)) $notif_dimanfaatkan->links()->createMany($links);
                Mail::to($dimanfaatkan[0]->kabupaten_pj_email)->send(new NotificationEmail([...$notif_dimanfaatkan_data, 'links' => $links]));
            }
        }
    }
}
