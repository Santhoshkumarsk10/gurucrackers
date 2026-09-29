<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductTamilNameSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Update Categories with Tamil Names
        $categoryTamilNames = [
            'One Sount Crackers' => 'ஒற்றை வெடி',
            'Flower Pots' => 'பூந்தொட்டி',
            'Ground Chakkaram' => 'தரை சக்கரம்',
            'Twinkling Star' => 'மின்மினி நட்சத்திரம்',
            'New Veriety' => 'புது ரகங்கள்',
            'Bijili Crackers' => 'பிஜிலி வெடி',
            'Auto Bombs' => 'ஆட்டோ பாம்',
            'Rockets' => 'ராக்கெட்',
            'Colour Matches' => 'வண்ண தீக்குச்சிகள்',
            'Variety Fountain' => 'வண்ண நீரூற்று',
            'Crackling Fountain' => 'சரவெடி பவுண்டன்',
            'Shower Variety' => 'மழை ரகங்கள்',
            'Candle Variety' => 'மெழுகுவர்த்தி ரகங்கள்',
            'Night Variety' => 'இரவு காட்சி ரகங்கள்',
            'Guru' => 'குரு ஸ்பெஷல்',
            'Single Shot Variety' => 'சிங்கிள் ஷாட்',
            'Multi Crackers' => 'மல்டி ஷாட்ஸ்',
            'Giftbox' => 'கிப்ட் பாக்ஸ்',
            'Sparklers' => 'கம்பி மத்தாப்பு',
            'Combo Offer' => 'காம்போ ஆஃபர்',
        ];

        foreach ($categoryTamilNames as $enName => $taName) {
            Category::where('name', $enName)->update(['tamil_name' => $taName]);
        }

        // 2. Specific product mappings
        $productTamilMap = [
            // Sound
            'Gold Lakshmi' => 'தங்க லக்ஷ்மி',
            '4" Deluxe Lakshmi' => '4" டீலக்ஸ் லக்ஷ்மி',
            '4" Lakshmi' => '4" லக்ஷ்மி',
            '3½ \'\' Lakshmi' => '3½" லக்ஷ்மி',
            '2¾" Kuruvi' => '2¾" குருவி வெடி',
            '5" Jallikattu (Bull)' => '5" ஜல்லிக்கட்டு (காளை)',
            '6" Jallikattu' => '6" ஜல்லிக்கட்டு',
            '2 Sound Crackers' => '2 சவுண்ட் கிராக்கர்ஸ்',

            // Flower pots
            'Flower Pot Big' => 'பெரிய பூந்தொட்டி',
            'Flower Pot Asoka' => 'அசோகா பூந்தொட்டி',
            'Color Koti' => 'கலர் கோட்டி',
            'Color Koti Deluxe Violet' => 'டீலக்ஸ் வயலட் கலர் கோட்டி',
            'Celebration Color Koti' => 'செலிபிரேஷன் கலர் கோட்டி',
            'Tower Pots' => 'டவர் பாட்ஸ்',
            'XXX Fast ( R & G )' => 'XXX பாஸ்ட் (சிவப்பு & பச்சை)',
            'Five Colour Fauntain (5 P)' => '5 கலர் பவுண்டன் (5 பீஸ்)',
            'Tri Colour ( 5 P)' => 'மூவர்ண பவுண்டன் (5 பீஸ்)',
            'Two Colour&White House' => 'டூ கலர் & ஒயிட் ஹவுஸ்',
            'Oyolo' => 'ஒயோலோ பவுண்டன்',

            // Chakkar
            'Ground Chakkar Big' => 'பெரிய தரை சக்கரம்',
            'Ground Chakkar Special' => 'ஸ்பெஷல் தரை சக்கரம்',
            'Ground Chakkaram Deluxe' => 'டீலக்ஸ் தரை சக்கரம்',
            'Wire Chakkaram' => 'கம்பி சக்கரம்',
            'Whishling weel' => 'விசிலிங் வீல்',
            'Win Wheel Nano' => 'வின் வீல் நானோ',
            'Race Wheel' => 'ரேஸ் வீல்',

            // Twinkling Star
            '1½" Twinkling Star' => '1½" மின்மினி நட்சத்திரம்',
            '4" Twinkling Star' => '4" மின்மினி நட்சத்திரம்',

            // New Variety
            'Cylinder Bomb (2 Pcs)' => 'சிலிண்டர் பாம் (2 பீஸ்)',
            'Malaai Kulfi ( 3 Pcs)' => 'மலாய் குல்பி (3 பீஸ்)',
            'Dragon & Peacock Touch (Mix)' => 'டிராகன் & மயில் டச்',
            'Speed Faster R15 ( 2 Pcs)' => 'ஸ்பீடு பாஸ்டர் R15 (2 பீஸ்)',
            'Jolly Bolly Mocktail (3 Pcs)' => 'ஜாலி பாலி மாக்டெயில் (3 பீஸ்)',

            // Bijili
            'Red Bijili ( 100 Pcs)' => 'சிவப்பு பிஜிலி (100 பீஸ்)',
            'Stripped Bijili ( 100 Pcs)' => 'வரி பிஜிலி (100 பீஸ்)',
            'Peacock Bijili ( 100 Pcs )' => 'மயில் பிஜிலி (100 பீஸ்)',

            // Auto Bombs
            'Bullet Bomb' => 'புல்லட் பாம்',
            'Hydro Bomb' => 'ஹைட்ரோ பாம்',
            'Classic Bomb' => 'கிளாசிக் பாம்',
            'Dinosar Bomb' => 'டைனோசர் பாம்',
            'Dinosour Bomb' => 'டைனோசர் பெரிய பாம்',
            'Small Bomb' => 'சிறிய பாம்',
            'Mediam Bomb' => 'நடுத்தர பாம்',
            'Big Bomb' => 'பெரிய பாம்',

            // Rockets
            'Rocket Bomb Colour' => 'கலர் ராக்கெட் பாம்',
            'Whistling Rocket' => 'விசிலிங் ராக்கெட்',
            '2 Sound Rocket' => '2 சவுண்ட் ராக்கெட்',

            // Matches
            '5 Star Matches' => '5 ஸ்டார் தீக்குச்சி',
            'Lap top VIP' => 'லேப்டாப் வி.ஐ.பி',

            // Fountains
            'Spooky' => 'ஸ்பூக்கி பவுண்டன்',
            'Old is Best' => 'ஓல்ட் இஸ் பெஸ்ட்',
            'Min mini' => 'மின்மினி',
            'Whizz Chakkar' => 'விஸ் சக்கரம்',
            'Cascode Candle' => 'கேஸ்கோடு கேண்டில்',
            'Google Galata' => 'கூகுள் கலாட்டா',
            'Love Dose' => 'லவ் டோஸ்',
            'Pappu Shower' => 'பப்பு ஷவர்',
            'Barbie Sky' => 'பார்பி ஸ்கை',
            'Two Fountain' => 'இரட்டை பவுண்டன்',
            '3 in Tin (Special)' => '3 இன் டின் (ஸ்பெஷல்)',
            'Carnival ( 2 Pcs)' => 'கார்னிவல் (2 பீஸ்)',

            // Crackling
            'Vettaiyan Crackling' => 'வேட்டையன் கிராக்லிங்',
            'White Crackling' => 'வெள்ளை கிராக்லிங்',
            'King Crackling' => 'கிங் கிராக்லிங்',
            'Sky King Fountain (Mix)' => 'ஸ்கை கிங் பவுண்டன்',
            'Red Sound & Bule ice (Mix)' => 'ரெட் சவுண்ட் & புளூ ஐஸ்',
            'Carnival & Twieer Bird (Mix)' => 'கார்னிவல் ட்விட்டர் பறவை',
            'Volvo Red & Gold Moments (Mix)' => 'வோல்வோ ரெட் & கோல்ட்',
            'Jolly Rancher' => 'ஜாலி ராஞ்சர்',
            'Two UP' => 'டூ அப்',
            'Three UP' => 'த்ரீ அப்',
            'Free Fire' => 'ப்ரீ பயர்',
            'Red Apple & Mr. Big (Mix)' => 'ரெட் ஆப்பிள் & மிஸ்டர் பிக்',

            // Shower
            'Machan Shower' => 'மச்சான் ஷவர்',
            'Star Pots Shower' => 'ஸ்டார் பாட்ஸ் ஷவர்',
            'Colour Rain Shower' => 'கலர் மழை ஷவர்',
            'Golden Globe Shower' => 'தங்க குளோப் ஷவர்',
            'Batter Shower' => 'பேட்டர் ஷவர்',
            'Peacock Feather' => 'மயில் இறகு',
            'Small Beacock' => 'சிறிய மயில்',
            'Bada Beacock' => 'பெரிய மயில்',
            'Magic Wips' => 'மேஜிக் விப்ஸ்',
            'Silver Drops' => 'வெள்ளித் துளிகள்',
            'Cannon Ball' => 'பீரங்கி குண்டு',
            'Luxery Peacock' => 'லக்ஸரி மயில்',

            // Candle
            'Magic Gold' => 'மேஜிக் கோல்ட்',
            'Minions & Trix (Mix)' => 'மினியன்ஸ் & டிரிக்ஸ்',
            'Smoke Stick' => 'வண்ண புகை குச்சி',
            'Selbe Stick' => 'செல்பி ஸ்டிக்',
            'Jungle Music & Funtain (Mix)' => 'ஜங்கிள் மியூசிக் பவுண்டன்',

            // Night
            'Helicopter' => 'ஹெலிகாப்டர்',
            'butter Fly' => 'வண்ணத்துப் பூச்சி',
            'Banbaram' => 'பம்பரம்',
            'Siren' => 'சைரன்',
            'Photo Flash' => 'போட்டோ பிளாஷ்',
            'Green Stone' => 'பச்சைக்கல் வெளிச்சம்',
            'Rotation Gun Small' => 'சுழலும் துப்பாக்கி',
            'Ring Cap' => 'ரிங் கேப்',

            // Guru
            'Guru 100' => 'குரு 100 வாலா சரவெடி',
            'Guru 1' => 'குரு 1K சரவெடி',
            'Guru 2' => 'குரு 2K சரவெடி',
            'Guru 5' => 'குரு 5K சரவெடி',
            'Guru 10' => 'குரு 10K சரவெடி',

            // Single shot
            '7 Shot' => '7 ஷாட்',
            'Chotta Fancy' => 'சோட்டா பேன்சி',
            '2" Single Shot' => '2" சிங்கிள் ஷாட்',
            '2 ¾ " Single Shot' => '2 ¾" சிங்கிள் ஷாட்',
            '2" Combo Fancy (3 Pcs)' => '2" காம்போ பேன்சி (3 பீஸ்)',
            '3½" Single Shot' => '3½" சிங்கிள் ஷாட்',
            '3½" Wedding Special' => '3½" திருமண ஸ்பெஷல்',
            '4" Fancy Single Shot' => '4" பேன்சி சிங்கிள் ஷாட்',
            '4" Fancy (2 Pcs)' => '4" பேன்சி (2 பீஸ்)',
            '5" Fancy (2 Pcs)' => '5" பேன்சி (2 பீஸ்)',
            '6" Fancy (2 Pcs)' => '6" பேன்சி (2 பீஸ்)',
            'Nayakara Falls (2 Pcs)' => 'நயாகரா நீர்வீழ்ச்சி (2 பீஸ்)',
            '4\'\' city Jumper (SPecial)' => '4" சிட்டி ஜம்பர் (ஸ்பெஷல்)',
            'Penta Shot' => 'பென்டா ஷாட்',

            // Multi
            '12 Shot' => '12 ஷாட்ஸ்',
            '25 Shot' => '25 ஷாட்ஸ்',
            '30 Shot' => '30 ஷாட்ஸ்',
            '60 Shot' => '60 ஷாட்ஸ்',
            '100 Shot' => '100 ஷாட்ஸ்',
            '120 Shot' => '120 ஷாட்ஸ்',
            '240 Shot' => '240 ஷாட்ஸ்',
            '10x10 Shot' => '10x10 ஷாட்ஸ்',
            '2" Set Out' => '2" செட் அவுட்',
            '3½" Set Out' => '3½" செட் அவுட்',

            // Giftbox
            '21 ITEM GIFT BOX' => '21 பொருட்கள் கொண்ட கிப்ட் பாக்ஸ்',
            '31 ITEM GIFT BOX' => '31 பொருட்கள் கொண்ட கிப்ட் பாக்ஸ்',
            '41 ITEM GIFT BOX' => '41 பொருட்கள் கொண்ட கிப்ட் பாக்ஸ்',
            '51 ITEM GIFT BOX' => '51 பொருட்கள் கொண்ட கிப்ட் பாக்ஸ்',
            '61 ITEM GIFT BOX' => '61 பொருட்கள் கொண்ட கிப்ட் பாக்ஸ்',

            // Sparklers
            '10 CM ELECTRIC SPARKLERS' => '10 செ.மீ எலக்ட்ரிக் மத்தாப்பு',
            '10 CM COLOUR SPARKLERS' => '10 செ.மீ கலர் மத்தாப்பு',
            '10 CM GREEN SPARKLERS' => '10 செ.மீ பச்சை மத்தாப்பு',
            '10 CM RED SPARKLERS' => '10 செ.மீ சிவப்பு மத்தாப்பு',
            '12 CM ELECTRIC SPARKLERS' => '12 செ.மீ எலக்ட்ரிக் மத்தாப்பு',
            '12 CM COLOUR SPARKLERS' => '12 செ.மீ கலர் மத்தாப்பு',
            '12 CM GREEN SPARKLERS' => '12 செ.மீ பச்சை மத்தாப்பு',
            '12 CM RED SPARKLERS' => '12 செ.மீ சிவப்பு மத்தாப்பு',
            '15 CM ELECTRIC SPARKLERS' => '15 செ.மீ எலக்ட்ரிக் மத்தாப்பு',
            '15 CM COLOUR SPARKLERS' => '15 செ.மீ கலர் மத்தாப்பு',
            '15 CM GREEN SPARKLERS' => '15 செ.மீ பச்சை மத்தாப்பு',
            '15 CM RED SPARKLERS' => '15 செ.மீ சிவப்பு மத்தாப்பு',
            '30CM ELECTRIC SPARKLERS' => '30 செ.மீ எலக்ட்ரிக் மத்தாப்பு',
            '30 CM COLOUR SPARKLERS' => '30 செ.மீ கலர் மத்தாப்பு',
            '30 CM GREEN SPARKLERS' => '30 செ.மீ பச்சை மத்தாப்பு',
            '30 CM RED SPARKLERS' => '30 செ.மீ சிவப்பு மத்தாப்பு',
            '50 CM ELECTRIC SPARKLERS' => '50 செ.மீ எலக்ட்ரிக் மத்தாப்பு',
            '50 CM COLOUR SPARKLERS' => '50 செ.மீ கலர் மத்தாப்பு',
            'HEARTIN SPARKLERS' => 'ஹார்டின் ஸ்பார்க்லர்ஸ்',
            'UMBERLA SPARKLERS' => 'குடை மத்தாப்பு',

            // Combos
            'KIDS COMBO' => 'குழந்தைகளுக்கான காம்போ',
            'BACHELOR COMBO' => 'பேச்சிலர் காம்போ',
            'FAMILY COMBO' => 'குடும்ப காம்போ',
        ];

        foreach ($productTamilMap as $enName => $taName) {
            Product::where('name', $enName)->update(['tamil_name' => $taName]);
        }

        // For any remaining product without tamil name, generate from its category
        $remaining = Product::whereNull('tamil_name')->orWhere('tamil_name', '')->get();
        foreach ($remaining as $prod) {
            $catTamil = $categoryTamilNames[$prod->category] ?? $prod->category;
            $prod->update(['tamil_name' => $prod->name . ' (' . $catTamil . ')']);
        }
    }
}
