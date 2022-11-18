<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Dataset;
use Illuminate\Http\Request;

class NaiveBayesController extends Controller
{
    public function index()
    {
        $time = Carbon::now()->format('H:00');
        $day = Carbon::now()->dayOfWeek;

        return $this->naivebayes($time, $day);
    }

    public function naivebayes($uji_waktu, $uji_kode_hari)
    {
        $output = ['A', 'B', 'C', 'D', 'E', 'F'];
        $waktu = [
            '00:00',
            '01:00',
            '02:00',
            '03:00',
            '04:00',
            '05:00',
            '06:00',
            '07:00',
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00',
            '18:00',
            '19:00',
            '20:00',
            '21:00',
            '22:00',
            '23:00',
        ];
        $kode_hari = [1, 2, 3, 4, 5, 6, 7];

        $probabilitas_output = array();

        foreach($output as $item) {
            $jumlah_output = Dataset::where('output', $item)->count();
            $jumlah_data = Dataset::count();

            $probabilitas_output[$item] = $jumlah_output / $jumlah_data;
        }

        $probabilitas_waktu = array();

        foreach($waktu as $item) {
            foreach($output as $item2) {
                $jumlah_waktu_output = Dataset::where('output', $item2)->where('waktu', $item)->count();
                $jumlah_output = Dataset::where('output', $item2)->count();
                $probabilitas_waktu[$item][$item2] = $jumlah_waktu_output / $jumlah_output;
            }
        }

        $probabilitas_kode_hari = array();

        foreach($kode_hari as $item) {
            foreach($output as $item2) {
                $jumlah_kode_hari_output = Dataset::where('output', $item2)->where('kode_hari', $item)->count();
                $jumlah_output = Dataset::where('output', $item2)->count();
                $probabilitas_kode_hari[$item][$item2] = $jumlah_kode_hari_output / $jumlah_output;
            }
        }

        $data_uji = [
            'waktu' => $uji_waktu,
            'kode_hari' => $uji_kode_hari,
        ];

        $uji = array();

        foreach($output as $item) {
            $uji[$item]['output'] = $probabilitas_output[$item];
            $uji[$item]['waktu'] = $probabilitas_waktu[$data_uji['waktu']][$item];
            $uji[$item]['kode_hari'] = $probabilitas_kode_hari[$data_uji['kode_hari']][$item];

            $uji[$item] = array_product($uji[$item]);
        }

        return array_search(max($uji), $uji);
    }
}
