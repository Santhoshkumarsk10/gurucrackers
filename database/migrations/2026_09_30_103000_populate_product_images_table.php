<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            1 => 'products/prod_1_gold_lakshmi.webp',
            2 => 'products/prod_2_4_deluxe_lakshmi.webp',
            3 => 'products/prod_3_4_lakshmi.webp',
            4 => 'products/prod_4_3_lakshmi.webp',
            5 => 'products/prod_5_2_kuruvi.webp',
            6 => 'products/prod_6_5_jallikattu_bull.webp',
            7 => 'products/prod_7_6_jallikattu.webp',
            8 => 'products/prod_8_2_sound_crackers.webp',
            9 => 'products/prod_9_flower_pot_big.webp',
            10 => 'products/prod_10_flower_pot_asoka.webp',
            11 => 'products/prod_11_color_koti.webp',
            12 => 'products/prod_12_color_koti_deluxe_violet.webp',
            13 => 'products/prod_13_celebration_color_koti.webp',
            14 => 'products/prod_14_tower_pots.webp',
            15 => 'products/prod_15_xxx_fast_r_g.webp',
            16 => 'products/prod_16_five_colour_fauntain_5_p.webp',
            17 => 'products/prod_17_tri_colour_5_p.webp',
            18 => 'products/prod_18_two_colour_white_house.webp',
            19 => 'products/prod_19_oyolo.webp',
            20 => 'products/prod_20_ground_chakkar_big.webp',
            21 => 'products/prod_21_ground_chakkar_special.webp',
            22 => 'products/prod_22_ground_chakkaram_deluxe.webp',
            23 => 'products/prod_23_wire_chakkaram.webp',
            24 => 'products/prod_24_whishling_weel.webp',
            25 => 'products/prod_25_win_wheel_nano.webp',
            26 => 'products/prod_26_race_wheel.webp',
            27 => 'products/prod_27_1_twinkling_star.webp',
            28 => 'products/prod_28_4_twinkling_star.webp',
            29 => 'products/prod_29_cylinder_bomb_2_pcs.webp',
            30 => 'products/prod_30_malaai_kulfi_3_pcs.webp',
            31 => 'products/prod_31_dragon_peacock_touch_mix.webp',
            32 => 'products/prod_32_speed_faster_r15_2_pcs.webp',
            33 => 'products/prod_33_jolly_bolly_mocktail_3_pcs.webp',
            34 => 'products/prod_34_red_bijili_100_pcs.webp',
            35 => 'products/prod_35_stripped_bijili_100_pcs.webp',
            36 => 'products/prod_36_peacock_bijili_100_pcs.webp',
            37 => 'products/prod_37_bullet_bomb.webp',
            38 => 'products/prod_38_hydro_bomb.webp',
            39 => 'products/prod_39_classic_bomb.webp',
            40 => 'products/prod_40_dinosar_bomb.webp',
            41 => 'products/prod_41_dinosour_bomb.webp',
            42 => 'products/prod_42_small_bomb.webp',
            43 => 'products/prod_43_mediam_bomb.webp',
            44 => 'products/prod_44_big_bomb.webp',
            45 => 'products/prod_45_rocket_bomb_colour.webp',
            46 => 'products/prod_46_whistling_rocket.webp',
            47 => 'products/prod_47_2_sound_rocket.webp',
            48 => 'products/prod_48_5_star_matches.webp',
            49 => 'products/prod_49_lap_top_vip.webp',
            50 => 'products/prod_50_spooky.webp',
            51 => 'products/prod_51_old_is_best.webp',
            52 => 'products/prod_52_min_mini.webp',
            53 => 'products/prod_53_whizz_chakkar.webp',
            54 => 'products/prod_54_cascode_candle.webp',
            55 => 'products/prod_55_google_galata.webp',
            56 => 'products/prod_56_love_dose.webp',
            57 => 'products/prod_57_pappu_shower.webp',
            58 => 'products/prod_58_barbie_sky.webp',
            59 => 'products/prod_59_two_fountain.webp',
            60 => 'products/prod_60_3_in_tin_special.webp',
            61 => 'products/prod_61_carnival_2_pcs.webp',
            62 => 'products/prod_62_vettaiyan_crackling.webp',
            63 => 'products/prod_63_white_crackling.webp',
            64 => 'products/prod_64_king_crackling.webp',
            65 => 'products/prod_65_sky_king_fountain_mix.webp',
            66 => 'products/prod_66_red_sound_bule_ice_mix.webp',
            67 => 'products/prod_67_carnival_twieer_bird_mix.webp',
            68 => 'products/prod_68_volvo_red_gold_moments_mix.webp',
            69 => 'products/prod_69_jolly_rancher.webp',
            70 => 'products/prod_70_two_up.webp',
            71 => 'products/prod_71_three_up.webp',
            72 => 'products/prod_72_free_fire.webp',
            73 => 'products/prod_73_red_apple_mr_big_mix.webp',
            74 => 'products/prod_74_machan_shower.webp',
            75 => 'products/prod_75_star_pots_shower.webp',
            76 => 'products/prod_76_colour_rain_shower.webp',
            77 => 'products/prod_77_golden_globe_shower.webp',
            78 => 'products/prod_78_batter_shower.webp',
            79 => 'products/prod_79_peacock_feather.webp',
            80 => 'products/prod_80_small_beacock.webp',
            81 => 'products/prod_81_bada_beacock.webp',
            82 => 'products/prod_82_magic_wips.webp',
            83 => 'products/prod_83_silver_drops.webp',
            84 => 'products/prod_84_cannon_ball.webp',
            85 => 'products/prod_85_luxery_peacock.webp',
            86 => 'products/prod_86_magic_gold.webp',
            87 => 'products/prod_87_minions_trix_mix.webp',
            88 => 'products/prod_88_smoke_stick.webp',
            89 => 'products/prod_89_selbe_stick.webp',
            90 => 'products/prod_90_jungle_music_fountain_mix.webp',
            91 => 'products/prod_91_helicopter.webp',
            92 => 'products/prod_92_butter_fly.webp',
            93 => 'products/prod_93_banbaram.webp',
            94 => 'products/prod_94_siren.webp',
            95 => 'products/prod_95_photo_flash.webp',
            96 => 'products/prod_96_green_stone.webp',
            97 => 'products/prod_97_rotation_gun_small.webp',
            98 => 'products/prod_98_ring_cap.webp',
            99 => 'products/prod_99_guru_100.webp',
            100 => 'products/prod_100_guru_1.webp',
            101 => 'products/prod_101_guru_2.webp',
            102 => 'products/prod_102_guru_5.webp',
            103 => 'products/prod_103_guru_10.webp',
            104 => 'products/prod_104_7_shot.webp',
            105 => 'products/prod_105_chotta_fancy.webp',
            106 => 'products/prod_106_2_single_shot.webp',
            107 => 'products/prod_107_2_single_shot.webp',
            108 => 'products/prod_108_2_combo_fancy_3_pcs.webp',
            109 => 'products/prod_109_3_single_shot.webp',
            110 => 'products/prod_110_3_wedding_special.webp',
            111 => 'products/prod_111_4_fancy_single_shot.webp',
            112 => 'products/prod_112_4_fancy_2_pcs.webp',
            113 => 'products/prod_113_5_fancy_2_pcs.webp',
            114 => 'products/prod_114_6_fancy_2_pcs.webp',
            115 => 'products/prod_115_nayakara_falls_2_pcs.webp',
            116 => 'products/prod_116_4_city_jumper_special.webp',
            117 => 'products/prod_117_penta_shot.webp',
            118 => 'products/prod_118_12_shot.webp',
            119 => 'products/prod_119_25_shot.webp',
            120 => 'products/prod_120_30_shot.webp',
            121 => 'products/prod_121_60_shot.webp',
            122 => 'products/prod_122_100_shot.webp',
            123 => 'products/prod_123_120_shot.webp',
            124 => 'products/prod_124_240_shot.webp',
            125 => 'products/prod_125_10x10_shot.webp',
            126 => 'products/prod_126_2_set_out.webp',
            127 => 'products/prod_127_3_set_out.webp',
            128 => 'products/prod_128_21_item_gift_box.webp',
            129 => 'products/prod_129_31_item_gift_box.webp',
            130 => 'products/prod_130_41_item_gift_box.webp',
            131 => 'products/prod_131_51_item_gift_box.webp',
            132 => 'products/prod_132_61_item_gift_box.webp',
            133 => 'products/prod_133_10_cm_electric_sparklers.webp',
            134 => 'products/prod_134_10_cm_colour_sparklers.webp',
            135 => 'products/prod_135_10_cm_green_sparklers.webp',
            136 => 'products/prod_136_10_cm_red_sparklers.webp',
            137 => 'products/prod_137_12_cm_electric_sparklers.webp',
            138 => 'products/prod_138_12_cm_colour_sparklers.webp',
            139 => 'products/prod_139_12_cm_green_sparklers.webp',
            140 => 'products/prod_140_12_cm_red_sparklers.webp',
            141 => 'products/prod_141_15_cm_electric_sparklers.webp',
            142 => 'products/prod_142_15_cm_colour_sparklers.webp',
            143 => 'products/prod_143_15_cm_green_sparklers.webp',
            144 => 'products/prod_144_15_cm_red_sparklers.webp',
            145 => 'products/prod_145_30cm_electric_sparklers.webp',
            146 => 'products/prod_146_30_cm_colour_sparklers.webp',
            147 => 'products/prod_147_30_cm_green_sparklers.webp',
            148 => 'products/prod_148_30_cm_red_sparklers.webp',
            149 => 'products/prod_149_50_cm_electric_sparklers.webp',
            150 => 'products/prod_150_50_cm_colour_sparklers.webp',
            151 => 'products/prod_151_heartin_sparklers.webp',
            152 => 'products/prod_152_umberla_sparklers.webp',
            153 => 'products/prod_153_kids_combo.webp',
            154 => 'products/prod_154_bachelor_combo.webp',
            155 => 'products/prod_155_family_combo.webp',
            158 => 'products/prod_158_flower_pot_special.webp',
        ];

        foreach ($map as $id => $path) {
            DB::table('products')->where('id', $id)->update(['image' => $path]);
        }

        try {
            $files = Storage::disk('public')->files('products');
            $webpFiles = array_filter($files, fn($f) => str_ends_with($f, '.webp'));
            $products = DB::table('products')->whereNull('image')->orWhere('image', '')->get();
            foreach ($products as $p) {
                $prefix = 'products/prod_' . $p->id . '_';
                foreach ($webpFiles as $file) {
                    if (str_starts_with($file, $prefix)) {
                        DB::table('products')->where('id', $p->id)->update(['image' => $file]);
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
    }
};
