<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticDataController extends Controller
{
    public function getUnits()
    {
        $units = [
            ['label' => 'Square Metre', 'value' => 'M2'],
            ['label' => 'Can', 'value' => 'CA'],
            ['label' => 'Plate', 'value' => 'PG'],
            ['label' => 'Roll', 'value' => 'RO'],
            ['label' => 'Number', 'value' => 'NO'],
            ['label' => 'Barrel', 'value' => 'BLL'],
            ['label' => 'Packet', 'value' => 'PA'],
            ['label' => 'Cap', 'value' => 'AV'],
            ['label' => 'Kilometre', 'value' => 'KTM'],
            ['label' => 'Kilo-Gramme', 'value' => 'KG'],
            ['label' => 'Dozen', 'value' => 'DZ'],
            ['label' => 'Kilowatt', 'value' => 'KWT'],
            ['label' => 'Part per Thousand', 'value' => 'NX'],
            ['label' => 'Bag', 'value' => 'BG'],
            ['label' => 'Pound', 'value' => 'LBR'],
            ['label' => 'Pieces/Item', 'value' => 'U'],
            ['label' => 'Drum', 'value' => 'DR'],
            ['label' => 'Pair', 'value' => '4B'],
            ['label' => 'Carat', 'value' => 'CR'],
            ['label' => 'Tube', 'value' => 'TU'],
            ['label' => 'Box', 'value' => 'BX'],
            ['label' => 'Set', 'value' => 'SET'],
            ['label' => 'Metre', 'value' => 'MTR'],
            ['label' => 'Gram', 'value' => 'GRM'],
            ['label' => 'Gallon', 'value' => 'GLL'],
            ['label' => 'Cell', 'value' => 'CEL'],
            ['label' => 'Block', 'value' => 'BL'],
            ['label' => 'Megawatt Hour', 'value' => 'MWT'],
            ['label' => 'Bundle', 'value' => 'BE'],
            ['label' => 'Yard', 'value' => 'YRD'],
            ['label' => 'Cubic Metre', 'value' => 'M3'],
            ['label' => 'Litre', 'value' => 'L'],
            ['label' => 'Milligram', 'value' => 'MGM'],
            ['label' => 'Tonne', 'value' => 'TNE'],
            ['label' => 'Gross', 'value' => 'GRO'],
            ['label' => 'Sheet', 'value' => 'ST'],
            ['label' => 'Link', 'value' => 'LK'],
            ['label' => 'Centimetre', 'value' => 'CMT'],
            ['label' => 'Reel', 'value' => 'RL'],
        ];

        return response()->json($units);
    }
}