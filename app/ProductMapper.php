<?php

namespace App;


use App\Product\AbstractProduct;

class ProductMapper
{
    private static $products = [
        1 => 'Wheat',
        2 => 'Corn',
        3 => 'Koniczyna',
        4 => 'Rzepak',
        5 => 'Buraki pastewne',
        6 => 'Herbal',
        7 => 'Słoneczniki',
        8 => 'Bławatki',
        9 => 'Egg',
        10 => 'Milk',
        11 => 'Wool',
        12 => 'Honey',
        13 => 'Chwasty',
        14 => 'Pieńki',
        15 => 'Kamienie',
        16 => 'Karaluchy',
        17 => 'Carrot',
        18 => 'Cucumber',
        19 => 'Rzodkiewki',
        20 => 'Strawberry',
        21 => 'Tomato',
        22 => 'Onion',
        23 => 'Szpinak',
        24 => 'Kalafiory',
        25 => 'Majonez',
        26 => 'Potato',
        27 => 'Cheese',
        28 => 'Kłębki wełny',
        29 => 'Szparagi',
        30 => 'Candy',
        31 => 'Cukinie',
        32 => 'Jagody',
        33 => 'Maliny',
        34 => 'Porzeczki',
        35 => 'Jeżyny',
        36 => 'Mirabelki',
        37 => 'Apple',
        38 => 'Dynie',
        39 => 'Pear',
        40 => 'Cherry',
        41 => 'Śliwki',
        42 => 'Orzechy włoskie',
        43 => 'Oliwki',
        44 => 'Czerwona kapusta',
        45 => 'Gołębnik',
        46 => 'Strach na wróble',
        47 => 'Menhir',
        48 => 'Ławeczka',
        49 => 'Namiot',
        50 => 'Mushroom',
        51 => 'Zając',
        52 => 'Para kochanków',
        53 => 'Łóżko polowe',
        54 => 'Sarenka',
        55 => 'Kretowisko',
        56 => 'Taczka',
        57 => 'Kupa gnoju',
        58 => 'Korytko',
        59 => 'Rower',
        60 => 'Bala siana',
        61 => 'Kręgi w polu kukurydzy',
        62 => 'Drogowskaz',
        63 => 'Cat',
        64 => 'Sheep',
        65 => 'Mouse',
        66 => 'Farmer',
        67 => 'Kamienny anioł',
        68 => 'Dynia',
        69 => 'Fontanna',
        70 => 'Niebieska&nbsp;ławka',
        71 => 'Czerwona&nbsp;ławka',
        72 => 'Różany łuk',
        73 => 'Krasnal ogrodowy',
        74 => 'Drewniana toaleta',
        75 => 'Klomb w oponie',
        76 => 'Huśtawka z opony',
        77 => 'Drabinka do wspinania',
        78 => 'Drewutnia',
        79 => 'Parasol przeciwsłoneczny',
        80 => 'Grill ogrodowy',
        81 => 'Drewniany pług',
        82 => 'Niebieska&nbsp;owca',
        83 => 'Czerwona&nbsp;owca',
        84 => 'Zielona&nbsp;owca',
        85 => 'Gumowce',
        86 => 'Widły do gnoju',
        87 => 'Słomiany chochoł',
        88 => 'Mini ranczo',
        89 => 'Niebieski krasnal',
        90 => 'Sadzawka z żabami',
        91 => 'Rybka akwariowa',
        92 => 'Karma dla rybek',
        93 => 'Superkarma dla rybek',
        94 => 'Sanki',
        95 => 'Choinka',
        96 => 'Wieniec adwentowy',
        97 => 'Gwiazdka betlejemska',
        98 => 'Fajerwerki',
        99 => 'Huśtawka',
        100 => '',
        101 => '',
        102 => '',
        103 => '',
        104 => 'Żonkil',
        105 => 'Koszyk Wielkanocny',
        106 => 'Fontanna',
        107 => 'Winogrona',
        108 => 'Bodziszki',
        109 => 'Stokrotki',
        110 => 'Kozie mleko',
        111 => 'Jogurt',
        112 => 'Czosnek',
        113 => 'Chili',
        114 => 'Bazylia',
        115 => 'Borowiki',
        116 => 'Olej kukurydziany',
        117 => 'Olej słonecznikowy',
        118 => 'Olej rzepakowy',
        119 => 'Olej dyniowy',
        120 => 'Olej orzechowy',
        121 => 'Olej oliwkowy',
        122 => 'Olej czosnkowy',
        123 => 'Olej chili',
        124 => 'Olej bazyliowy',
        125 => 'Olej borowikowy',
        126 => 'Dalia',
        127 => 'Rabarbar',
        128 => 'Arbuzy',
        129 => 'Herbata',
        130 => 'Sok marchwiowy',
        131 => 'Sok pomidorowy',
        132 => 'Mleko truskawkowe',
        133 => 'Mleko rzodkiewkowe',
        134 => 'Sok marchwiowy z ziołami',
        135 => 'Sok pomidorowy z ziołami',
        136 => 'Prażona kukurydza',
        137 => 'Bagietka',
        138 => 'Bagietka szpinakowa',
        139 => 'Sałatka ogórkowa',
        140 => 'Sałatka pomidorowa',
        141 => 'Frytki z keczupem',
        142 => 'Frytki z majonezem',
        143 => 'Frytki keczup/majo',
        144 => 'Keczup',
        145 => 'Sok malinowy',
        146 => 'Mleko jeżynowe',
        147 => 'Sok jabłkowy',
        148 => 'Zapiekanka cukiniowa',
        149 => 'Sałatka włoska',
        150 => 'Zupa dyniowa',
        151 => 'Wełna angora',
        152 => 'Kłębki wełny angora',
        153 => 'Kalarepa',
        154 => 'Mlecz',
        155 => 'Skarpety',
        156 => 'Szal',
        157 => 'Czapka',
        158 => 'Pomarańczowy tulipan',
        159 => 'Karma dla koni',
        160 => 'Superbaton',
        161 => 'Ciasto marchewkowe',
        162 => 'Ciasto truskawkowe',
        163 => 'Ciasto cebulowe',
        164 => 'Rogalik z masłem',
        165 => 'Ukąszenie osy',
        166 => 'Sernik',
        167 => 'Tort wiśniowy',
        168 => 'Rolada orzechowa',
        169 => 'Ciasto ziołowe',
        170 => 'Chleb oliwkowy',
        171 => 'Oset',
        172 => 'Orchidea',
        173 => 'Lilia',
        174 => 'Róża',
        175 => 'Jaskier',
        176 => 'Chryzantema',
        177 => 'Bratek',
        178 => 'Kocanka',
        179 => 'Bazia',
        180 => 'Rumianek',
        181 => 'Mak',
        182 => 'Gipsówka',
        183 => 'Goździk',
        184 => 'Aster letni',
        185 => 'Bez',
        186 => 'Dynia ozdobna',
        187 => 'Hortensja',
        188 => 'Muchomor',
        189 => 'Lilia tygrysia',
        200 => 'Wykwintny wieniec łukowy',
        201 => 'Letnia wiązanka',
        202 => 'Piękna wiązanka stożkowa',
        203 => 'Wiązanka wiosenna',
        204 => 'Wiązanka stożkowa',
        205 => 'Barwny wieniec ozdobny',
        206 => 'Wieniec',
        207 => 'Wiązanka biedermeier',
        208 => 'Wielki wieniec łukowy',
        209 => 'Róg obfitości',
        210 => 'Wiązanka kulista',
        211 => 'Wytworny wieniec',
        212 => 'Letnia wiązanka kulista',
        213 => 'Jesienna wiązanka',
        214 => 'Szlachetna wiązanka łukowa',
        215 => 'Piękna wiązanka letnia',
        216 => 'Szlachetna wiązanka stożkowa',
        217 => 'Szlachetny wieniec ozdobny',
        218 => 'Szlachetny wieniec',
        219 => 'Piękna wiązanka jesienna',
        220 => 'Wiązanka muchomorowa',
        221 => 'Lilia tygrysia',
        250 => 'Aloes',
        251 => 'Arnika',
        252 => 'Oczar',
        253 => 'Mącznica',
        254 => 'Gomphocarpus',
        255 => 'Psianka',
        256 => 'Krwawnik',
        257 => 'Dzięgiel',
        258 => 'Lepiężnik',
        259 => 'Mięta',
        260 => 'Rosiczka',
        261 => 'Dziurawiec',
        262 => 'Łopian',
        263 => 'Serdecznik',
        264 => 'Nagietek',
        265 => 'Ostróżka',
        266 => 'Bluszcz',
        267 => 'Połonicznik',
        268 => 'Złocień',
        269 => 'Werbena',
        270 => 'Dzika róża',
        271 => 'Bez',
        272 => 'Ukwap',
        273 => 'Piwonia',
        274 => 'Tymianek',
        275 => 'Pnący się jałowiec',
        276 => 'Gruszyczka',
        300 => 'Nosuskuśtykus',
        301 => 'Magia Akinra',
        302 => 'Czar Aloesu',
        303 => 'Kaszluskatarus',
        304 => 'Połacińskus',
        305 => 'Nocny roślinowiec',
        306 => 'Achillea',
        307 => 'Cośnagały',
        308 => 'Lepiejżyć',
        309 => 'Mętuszielonus',
        310 => 'Miętasłońca',
        311 => 'Bezdziór',
        312 => 'Łojaknówka',
        313 => 'Sercówka',
        314 => 'Ipostrachus',
        315 => 'Ostróżzdrowia',
        316 => 'Bluszczówka',
        317 => 'Połonicznikus',
        318 => 'Złocienius',
        319 => 'Złota Werbena',
        320 => 'Dzikuszakwasus',
        321 => 'Hoha',
        322 => 'Nakrztusiec',
        323 => 'Hieronimus',
        324 => 'Tynktura z tymiankowca',
        325 => 'Koncentrat z nosuskuśtykusa',
        326 => 'Koncentrat z magii Akinra',
        327 => 'Koncentrat z czaru Aloesu',
        328 => 'Koncentrat z kaszluskatarusa',
        329 => 'Koncentrat z połacińskusa',
        330 => 'Tynktura z róż jałowca',
        331 => 'Koncentrat z nocnego roślinowca',
        332 => 'Koncentrat z achillei',
        333 => 'Koncentrat z cośnagały',
        334 => 'Koncentrat z lepiejżyć',
        335 => 'Koncentrat z tymiankowca',
        336 => 'Koncentrat z mętuszielonusa',
        337 => 'Koncentrat z miętasłońca',
        338 => 'Koncentrat z bezdzióra',
        339 => 'Koncentrat z łojaknówki',
        340 => 'Koncentrat z sercówki',
        341 => 'Koncentrat z ipostrachusa',
        342 => 'Koncentrat z ostróżzdrowia',
        343 => 'Koncentrat z bluszczówki',
        344 => 'Anielska zieleń',
        345 => 'Koncentrat z połonicznikusa',
        346 => 'Koncentrat ze złocieniusa',
        347 => 'Koncentrat ze złotej Werbeny',
        348 => 'Koncentrat z dzikuszakwasusa',
        349 => 'Koncentrat z hoha',
        350 => 'Biopaliwa',
        351 => 'Ananas',
        352 => 'Lima',
        353 => 'Liczi',
        354 => 'Papaja',
        355 => 'Marakuja',
        356 => 'Banan',
        357 => 'Kumkwat',
        358 => 'Oskomian',
        359 => 'Mango',
        360 => 'Kokos',
        361 => 'Pitaja',
        400 => 'Koncentrat z nakrztusca',
        401 => 'Koncentrat z hieronimusu',
        402 => 'Koncentrat z róż jałowca',
        403 => 'Koncentrat z anielskiej zieleni',
        450 => 'Lód truskawkowy',
        451 => 'Lód malinowy',
        452 => 'Sorbet porzeczkowy',
        453 => 'Lód jeżynowy',
        454 => 'Lód mirabelkowy',
        455 => 'Sorbet jabłkowy',
        456 => 'Lód wiśniowy',
        457 => 'Sorbet ananasowy',
        458 => 'Lód bananowy',
        459 => 'Lód pitajowy',
        460 => 'Lód kokosowy',
        461 => 'Lód kumkwat',
        462 => 'Sorbet limetkowy',
        463 => 'Lód liczi',
        464 => 'Lód mango',
        465 => 'Sorbet marakujowy',
        466 => 'Lód papajowy',
        467 => 'Lód oskomianowy',
        468 => 'Mleko bananowe',
        469 => 'Zielony smoothie',
        470 => 'Egzotyczny shake',
        471 => 'Pina colada',
        472 => 'Shake mleczno-kokosowy',
        473 => 'Drink cytrusowy',
        474 => 'Sałatka mleczowa',
        475 => 'Zapiekanka z kapusty',
        476 => 'Zupa grzybowa',
        477 => 'Tost hawajski',
        478 => 'Zapiekanka gruszkowa',
        479 => 'Ciasto śliwkowe',
        480 => 'Tort limetkowy',
        481 => 'Tort mango-marakuja',
        482 => 'Ciasto bananowe',
        483 => 'Ciasto rabarbarowe',
        550 => 'opalająca się krowa',
        551 => 'Zimowe drzewo',
        552 => 'Sakura',
        553 => 'Lodziarz',
        600 => 'Słodki kleik',
        601 => 'Jogurt',
        602 => 'Kleik zbożowy',
        603 => 'Mieszanka kwiatowa',
        604 => 'Papka jagodowa',
        605 => 'Pożywka proteinowa',
        606 => 'Kleik zielony',
        607 => 'Kleik warzywny',
        608 => 'Mus jabłkowy',
        609 => 'Mieszanka owocowa',
        630 => 'Cukierek na sznurku',
        631 => 'Ziemniaczany przyjaciel',
        632 => 'Marchewkowy bumerang',
        633 => 'Popcornowa piłka',
        634 => 'Ziołowa myszka',
        635 => 'Dyniarz',
        636 => 'Rzodkiewkowy cukierek',
        637 => 'Kalafiorowe zagadki',
        638 => 'Kalarepowa piłka',
        639 => 'Zestaw żonglera',
        660 => 'Pluszowa marchewka',
        661 => 'Rzepakowy pluszak',
        662 => 'Rzepakowy pisklak',
        663 => 'Kwiatowa poduszka',
        664 => 'Motylek',
        665 => 'Zbożowa poducha',
        666 => 'Kalafiorowy kumpel',
        667 => 'Słonecznikowe poduchy',
        668 => 'Rzepakowy Rysiu',
        669 => 'Czerwona owca',
        700 => 'Babka lancetowata',
        701 => 'Szałwia',
        702 => 'Pokrzywa',
        703 => 'Kminek',
        704 => 'Przywrotnik',
        705 => 'Ślaz',
        706 => 'Lawenda',
        707 => 'zielona mięta',
        708 => 'Melisa',
        709 => 'Dziewanna',
        750 => 'Herbata orzeźwiająca',
        751 => 'Herbata ożywiająca',
        752 => 'Herbata wędrowna',
        753 => 'Herbata sportowa',
        754 => 'Herbata fitness',
        755 => 'Herbata energetyzująca',
        756 => 'Herbata harmonizująca',
        757 => 'Herbata relaksująca',
        758 => 'Herbata zdrowotna',
        759 => 'Herbata na dobry humor',
    ];

    public static function getProductNameByPid($pid)
    {
        if (!isset(self::$products[$pid])) {
            throw new \Exception('Product id: ' . $pid . ' not found');
        }
        return self::$products[$pid];
    }
}