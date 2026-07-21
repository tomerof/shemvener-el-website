<?php
    $path = '/yad-vashem';
    $data = [];
    $rtl  = '0'; // English

    if( isset( $_GET['errors'])) {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    }

    function encryptData( $json) {
        $json = base64_encode( $json);
        $json = str_replace('=', '--S', $json);
        $json = str_replace('e', '.', $json);
        return $json;
    }

    function decryptData( $str) {
        $str = str_replace('--S', '=', $str);
        $str = str_replace('.', 'e', $str);
        return base64_decode( $str);
    }

    function get_son_of_text( $gender, $fatherName, $motherName, $personal_status, $partner_name, $lang = 'hebrew') {
        $prefix = '';
        $status = '';
        $addon  = '';
    
        if( ! $gender) return '';

        if( $personal_status == 'נשוי') {
            if( $gender == 'male') {
                if( $partner_name) {
                    $status = ( $lang && $lang !== 'english') ? "נשוי ל$partner_name" : "married to $partner_name";
                } else {
                    $status = 'נשוי';
                }

            } else {
                if( $partner_name) {
                    $status = ( $lang && $lang !== 'english') ? "נשואה ל$partner_name" : "married to $partner_name";
                } else {
                    $status = 'נשואה';
                }
            }
        }

        if( $gender == 'male') {
            $prefix = ( $lang == 'hebrew') ? 'בן ' : 'Son of ';

        } else {
            $prefix = ( $lang == 'hebrew') ? 'בת ' : 'Daughter of ';
        }
    
        if( $fatherName && $motherName) {
            $addon = ( $lang == 'hebrew') ? ' ו' : ' and ';

        } else if( ! $fatherName && ! $motherName) {
            return '';
        }
    
        $output = "$prefix $motherName". $addon ."$fatherName";
        if( $status) $output .= ", $status";
        return $output;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Yad Vashem - Shem Vener</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
        <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
        <link rel="stylesheet" href="<?= $path .'/assets/css/style.css?ver='. time(); ?>">
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Assistant:wght@200..800&display=swap");
            body{ font-family: "Assistant", sans-serif;width:1000px;}
            #label-print{display:block;width:1000px;margin:1rem auto;position:relative;}
            img{width:100%;height:auto}
            #wrap-qr_code{position:absolute;right:65px;bottom:35px; width:110px}
            #wrap-qr_code img{width:100%;}
            .inner-text{text-align:center;line-height: 11px;position:absolute;color:#000;left:0;right:0;font-size:20px;top:30px;width:295px;margin:0 auto 0 280px;padding-top:28px}
            h3{font-size:27px;font-weight:600;line-height:25px;margin:0;}
            p{line-height:1.2; margin: 0;}
            p.years {padding: 12px 0 3px 0;}
            .ltr .inner-text{font-size: 17.5px;}
            .wrap-all-d { display:block !important;}
            .charecters-error { display: none !important;}
        </style>

        <!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-NQ58P4Q');</script>
		<!-- End Google Tag Manager -->
    </head>

    <body class="yad-vashem fixed-header">
        <?php
            if( isset( $_GET['data']) && $_GET['data']) {
                $data = htmlspecialchars( $_GET['data']);

                // Decrypt
                $data = decryptData( $data);
                $parsed_query_params = [];
                $data = parse_str( $data, $parsed_query_params);
                $data = $parsed_query_params;

                // Lang
                if( ! isset( $data['lang']) || ! $data['lang']) {
                    $data['lang'] = 'hebrew';
                    $rtl = '1';
                }
            }
        ?>

        <!-- Header -->
        <div class="section-header">
            <div class="site-container">
                <div class="wrap">
                    <div class="logo" style="width: auto !important; height: auto !important; margin: 0 auto;">
                        <a href="https://www.yadvashem.org/he.html" title="<?= ( $data['lang'] == 'english') ? 'Yad Vashem' : 'יד ושם'; ?>">
                            <img src="shemvener-yadvashem-logo-he-part1.jpg" alt="">
                        </a>

                        <a href="https://shemvener.org.il/" title="<?= ( $data['lang'] == 'english') ? 'Shemvener' : 'שם ונר'; ?>">
                            <img src="shemvener-yadvashem-logo-he-part2.jpg" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Banner -->
        <div class="yad-vashem-main-banner">
            <?php if( $data['lang'] == 'english') { ?>
                <img width="1920" height="1080" src="https://shem-s3-public.s3.eu-west-1.amazonaws.com/wp-content/uploads/2024/02/13192121/banner-en.jpg">
                <p>On this commemoration page you can print a label for a memorial candle featuring the name of any victim who perished in the Holocaust.</p>
            <?php } else { ?>
                <img width="1920" height="1080" src="https://shem-s3-public.s3.eu-west-1.amazonaws.com/wp-content/uploads/2024/02/13183440/banner-he.jpg">
                <p>דף הנצחה זה הינו פלטפורמת זיכרון המאפשרת להדפיס תווית לנר נשמה עם כל אחד משמות הנספים שנספו בשואה.</p>
            <?php } ?>
        </div>

        <!-- Bottom text -->
        <div class="yad-vashem-banner-bottom-text"></div>

        <!-- Candles -->
        <div class="yad-vashem-candles-separator">
            <?php if( $data['lang'] == 'english') { ?>
                <img width="660" height="485" src="https://shem-s3-public.s3.eu-west-1.amazonaws.com/wp-content/uploads/2024/02/13192148/candles-en.jpg">
            <?php } else { ?>
                <img width="660" height="485" src="https://shem-s3-public.s3.eu-west-1.amazonaws.com/wp-content/uploads/2024/02/13183716/candles-he.jpg">
            <?php } ?>
        </div>

        <!-- Label -->
        <?php if( $data['lang'] == 'english') { ?>
            <h2>To print a label for a memorial candle, click here</h2>
        <?php } else { ?>
            <h2>להדפסת תווית לנר נשמה יש ללחוץ על הכפתור</h2>
        <?php } ?>

        <?php
            if( $data) {
                $field_mapping = array(
                    ['id' => 'father-name-input', 'name' => 'father_name'],
                    ['id' => 'mother-name-input', 'name' => 'mother_name'],
                    ['id' => 'partner-name-input', 'name' => 'partner_name'],
                    ['id' => 'kids-input', 'name' => 'number_of_kids'],
                    ['id' => 'age-input', 'name' => 'age-input'],
                    ['id' => 'personal_status', 'name' => 'personal_status'],
                    ['id' => 'notes-input', 'name' => 'family'],
                    ['id' => null, 'name' => 'first_name'],
                    ['id' => null, 'name' => 'last_name'],
                    ['id' => null, 'name' => 'gender'],
                    ['id' => 'year_of_birth', 'name' => 'year_of_birth'],
                    ['id' => 'year_of_death', 'name' => 'year_of_death'],
                    ['id' => 'family-input-trigger', 'name' => 'family_details'],
                    ['id' => 'sonOf', 'name' => 'sonOf'],
                    ['id' => 'marriedTofatherTo', 'name' => 'marriedTofatherTo'],
                    ['id' => 'notice', 'name' => 'notice'],
                    ['id' => null, 'name' => 'occupation'],
                    ['id' => null, 'name' => 'qr_code'],
                    ['id' => null, 'name' => 'born_city'],
                    ['id' => 'birth_country', 'name' => 'birth_country'],
                    ['id' => 'birth_country_hidden', 'name' => null],
                    ['id' => null, 'name' => 'place_of_death'],
                    ['id' => null, 'name' => 'more_info']
                );

                if( isset( $data['year_of_birth']) && ( ! $data['year_of_birth']) || intval( $data['year_of_birth']) < 999) {
                    $data['year_of_birth'] = '?';
                }
                if( isset( $data['year_of_death']) && ( ! $data['year_of_death']) || intval( $data['year_of_death']) < 999) {
                    $data['year_of_death'] = '?';
                }

                // Hotfix: qr_code
                if( isset( $data['itemId'])) {
                    $data['qr_code'] .= '&itemId='. $data['itemId'];
                    $data['qr_code'] = urlencode( $data['qr_code']);
                    unset( $data['itemId']);
                }

                // Reset
                if( ! isset( $data['gender'])) $data['gender'] = '';
                if( ! isset( $data['father_name'])) $data['father_name'] = '';
                if( ! isset( $data['mother_name'])) $data['mother_name'] = '';

                if( $data['mother_name'] && $data['family']) {
                    $data['mother_name'] .= ', '. trim( $data['family'], '.');
                }

                if( strlen( $data['place_of_death']) > 44) {
                    $data['place_of_death'] = substr( $data['place_of_death'], 0, 44);
                }

                if( isset( $_GET['debug'])) {
                    echo '<pre>';print_r($data);echo '</pre>';
                }

                // Mapping
                if( $data && is_array( $data)) {
                    // Magic starts here
                    if ( $data['lang'] == 'hebrew') {
                        $img = $path .'/assets/images/label.png';
                    } else {
                        $img = $path .'/assets/images/label-en2.png';
                    }
                    ?>
                        <div class="section-create-label-form-main-wrapper">
                            <input type="hidden" id="record-status" name="record-status" value="publish">

                            <!-- sonOf -->
                            <?php if( isset( $data['father_name']) && $data['father_name'] && ! isset( $data['sonOf'])) {
                                $v = get_son_of_text( $data['gender'], $data['father_name'], $data['mother_name'], $data['personal_status'], $data['partner_name'], $data['lang']);
                                echo "<input type=\"hidden\" id=\"sonOf\" name=\"sonOf\" value=\"$v\">";
                            } ?>

                            <?php
                                // Fields must match mapping
                                foreach( $field_mapping as $arr) {
                                    $id = $arr['id'];
                                    $name = $arr['name'];
                                    $skip = false;

                                    foreach( $data as $k => $v) {
                                        if( $id == $k || $name == $k && $v) {
                                            echo "<input type=\"hidden\" id=\"$id\" name=\"$name\" value=\"$v\">";
                                            $skip = true;
                                        }
                                    }

                                    if( $skip === false) {
                                        echo "<input type=\"hidden\" id=\"$id\" name=\"$name\" value=\"\">";
                                        $skip = false;
                                    }
                                }
                            ?>
                            <canvas id="label-cnavas" data-back-img="<?= $img; ?>" width="1300" height="405" style="border:1px solid #000000;"></canvas>
                        </div>
                    <?php

                } else {
                    header('HTTP/1.0 403 Forbidden');
                    die();
                }

            } else {
                header('HTTP/1.0 403 Forbidden');
                die();
            }
        ?>

        <button class="print-button"><?= ( $data['lang'] == 'english') ? 'Print a label for a candle' : 'להדפסת תווית לנר'; ?></button>

        <!-- Footer -->
        <?php if( $data['lang'] == 'english') { ?>
            <div class="yad-vashem-footer">
                <img width="1920" height="1440" src="https://shem-s3-public.s3.eu-west-1.amazonaws.com/wp-content/uploads/2024/02/14220631/hand-en-1.jpg">
            </div>
        <?php } else { ?>
            <div class="yad-vashem-footer">
                <img width="1920" height="1440" src="https://shem-s3-public.s3.eu-west-1.amazonaws.com/wp-content/uploads/2024/02/14220329/hand-he-1.jpg">
            </div>
        <?php } ?>

        <script src="jquery.min.js"></script>
        <script src="lib.js?ver=<?= time(); ?>"></script>
        <script src="functions.js?ver=<?= time(); ?>"></script>
        <script src="select2/_select2.js"></script>
        <script src="qrcode.min.js?ver=<?= time(); ?>"></script>
        <script src="sticker.js?ver=<?= time(); ?>"></script>
        <script src="scripts.js?ver=<?= time(); ?>"></script>

        <script>
            jQuery( function() {
                jQuery(window).scroll( function() {
                    var scroll = jQuery(window).scrollTop();
                    if( scroll > 100) {
                        jQuery('.section-header').addClass('scroll');
                    } else {
                        jQuery('.section-header').removeClass('scroll');
                    }
                });
            });

            const info = {
                rtl: <?= $rtl; ?>
            };

            const sticker = {
                "label": {
                    "city_lbl": "%%city%%",
                    "country_lbl": "%%country%%",
                    "city_country_lbl": "%%city%%, %%country%%",
                    "city_lbl_female": "%%city%%",
                    "country_lbl_female": "%%country%%",
                    "city_country_lbl_female": "%%city%%, %%country%%",
                    "occupation_lbl": "%%occupation%%",
                    "pod_lbl": "%%place_of_death%%",
                    "pod_lbl_female": "%%place_of_death%%",
                    "male_label": {
                        "blank": {
                            "father": "בן %%father_name%%",
                            "mother": "בן %%mother_name%%",
                            "father_mother": "בן %%father_name%% ו%%mother_name%%"
                        },
                        "unknown": {
                            "blank": "",
                            "father": "בן %%father_name%%.",
                            "mother": "בן %%mother_name%%.",
                            "father_mother": "בן %%father_name%% ו%%mother_name%%.",
                            "father_children": "בן %%father_name%%, אב ל-%%number_of_kids%%",
                            "mother_children": "בן %%mother_name%%, אב ל-%%number_of_kids%%",
                            "father_mother_children": "בן %%father_name%% ו%%mother_name%%, אב ל-%%number_of_kids%%"
                        },
                        "adult_single": {
                            "blank": "רווק",
                            "father": "בן %%father_name%%##lb##רווק.",
                            "mother": "בן %%mother_name%%##lb##רווק.",
                            "father_mother": "בן %%father_name%% ו%%mother_name%%##lb##רווק."
                        },
                        "married": {
                            "blank": " נשוי",
                            "father": "בן %%father_name%%##lb##נשוי",
                            "mother": "בן %%mother_name%%##lb##נשוי",
                            "father_mother": "בן %%father_name%% ו%%mother_name%%##lb##נשוי",
                            "father_children": "בן %%father_name%%##lb##נשוי, אב ל-%%number_of_kids%%",
                            "mother_children": "בן %%mother_name%%##lb##נשוי, אב ל-%%number_of_kids%%",
                            "father_mother_children": "בן %%father_name%% ו%%mother_name%%##lb##נשוי, אב ל-%%number_of_kids%%",
                            "father_spouse": "בן %%father_name%%##lb##נשוי ל%%partner_name%%",
                            "mother_spouse": "בן %%mother_name%%##lb##נשוי ל%%partner_name%%",
                            "father_mother_spouse": "בן %%father_name%% ו%%mother_name%%##lb## נשוי ל%%partner_name%%",
                            "father_spouse_children": "בן %%father_name%%##lb##נשוי ל%%partner_name%%, אב ל-%%number_of_kids%%",
                            "mother_spouse_children": "בן %%mother_name%%##lb##נשוי ל%%partner_name%%, אב ל-%%number_of_kids%%",
                            "father_mother_spouse_children": "בן %%father_name%% ו%%mother_name%%##lb##נשוי ל%%partner_name%%, אב ל-%%number_of_kids%%",
                            "spouse": "נשוי ל%%partner_name%%",
                            "spouse_children": "נשוי ל%%partner_name%%, אב ל-%%number_of_kids%%",
                            "children": "נשוי, אב ל-%%number_of_kids%% ילדים"
                        },
                        "widow": {
                            "blank": "אלמן",
                            "father": "בן %%father_name%%##lb##אלמן",
                            "mother": "בן %%mother_name%%##lb##אלמן",
                            "father_mother": "בן %%father_name%% ו%%mother_name%%##lb##אלמן",
                            "father_children": "בן %%father_name%%##lb##אלמן, אב ל-%%number_of_kids%%",
                            "mother_children": "בן %%mother_name%%##lb##אלמן, אב ל-%%number_of_kids%%",
                            "father_mother_children": "בן %%father_name%% ו%%mother_name%%##lb##אלמן, אב ל-%%number_of_kids%%.",
                            "father_spouse": "בן %%father_name%%##lb##התאלמן מ%%partner_name%%",
                            "mother_spouse": "בן %%mother_name%%##lb##התאלמן מ%%partner_name%%",
                            "father_mother_spouse": "בן %%father_name%% ו%%mother_name%%##lb##התאלמן מ%%partner_name%%",
                            "father_spouse_children": "בן %%father_name%%##lb##התאלמן מ%%partner_name%%, אב ל-%%number_of_kids%%",
                            "mother_spouse_children": "בן %%mother_name%%##lb##התאלמן מ%%partner_name%%, אב ל-%%number_of_kids%%",
                            "father_mother_spouse_children": "בן %%father_name%% ו%%mother_name%%##lb##התאלמן מ%%partner_name%%, אב ל-%%number_of_kids%%",
                            "spouse": "אלמן ל%%partner_name%%",
                            "spouse_children": "אלמן ל%%partner_name%%, אב ל-%%number_of_kids%%",
                            "children": "אלמן, אב ל-%%number_of_kids%% ילדים"
                        },
                        "divorcee": {
                            "blank": "גרוש",
                            "father": "בן %%father_name%%##lb##גרוש",
                            "mother": "בן %%mother_name%%##lb##גרוש",
                            "father_mother": "בן %%mother_name%% %%father_name%%##lb##גרוש",
                            "father_children": "בן %%father_name%%##lb##גרוש, אב ל-%%number_of_kids%%",
                            "mother_children": "בן %%mother_name%%##lb##גרוש, אב ל-%%number_of_kids%%",
                            "father_mother_children": "בן %%mother_name%% %%father_name%%##lb## גרוש, אב ל-%%number_of_kids%%",
                            "children": "גרוש, אב ל-%%number_of_kids%%"
                        },
                        "children_only": "אב ל-%%number_of_kids%% ילדים"
                    },
                    "female_label": {
                        "blank": {
                            "father": "בת %%father_name%%",
                            "mother": "בת %%mother_name%%",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%"
                        },
                        "unknown": {
                            "blank": "",
                            "father": "בת %%father_name%%",
                            "mother": "בת %%mother_name%%",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%",
                            "father_children": "בת %%father_name%%, אם ל-%%number_of_kids%%",
                            "mother_children": "בת %%mother_name%%, אם ל-%%number_of_kids%%",
                            "father_mother_children": "בת %%father_name%% ו%%mother_name%%, אם ל-%%number_of_kids%%"
                        },
                        "adult_single": {
                            "blank": "רווקה",
                            "father": "בת %%father_name%%##lb##רווקה",
                            "mother": "בת %%mother_name%%##lb##רווקה",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%##lb##רווקה"
                        },
                        "married": {
                            "blank": "נשואה",
                            "father": "בת %%father_name%%##lb##נשואה",
                            "mother": "בת %%mother_name%%##lb##נשואה",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%##lb##נשואה",
                            "father_children": "בת %%father_name%%##lb##נשואה, אם ל-%%number_of_kids%%",
                            "mother_children": "בת %%mother_name%%##lb##נשואה, אם ל-%%number_of_kids%%",
                            "father_mother_children": "בת %%father_name%% ו%%mother_name%%##lb##נשואה, אם ל-%%number_of_kids%%",
                            "father_spouse": "בת %%father_name%%##lb##נשואה ל%%partner_name%%",
                            "mother_spouse": "בת %%mother_name%%##lb##נשואה ל%%partner_name%%",
                            "father_mother_spouse": "בת %%father_name%% ו%%mother_name%%##lb##נשואה ל%%partner_name%%",
                            "father_spouse_children": "בת %%father_name%%##lb##נשואה ל%%partner_name%%, אם ל-%%number_of_kids%%",
                            "mother_spouse_children": "בת %%mother_name%%##lb##נשואה ל%%partner_name%%, אם ל-%%number_of_kids%%",
                            "father_mother_spouse_children": "בת %%father_name%% ו%%mother_name%%##lb##נשואה ל%%partner_name%%, אם ל-%%number_of_kids%%",
                            "spouse": "נשואה ל%%partner_name%% ",
                            "spouse_children": "נשואה ל%%partner_name%%, אם ל-%%number_of_kids%%",
                            "children": "נשואה, אם ל-%%number_of_kids%%"
                        },
                        "widow": {
                            "blank": "אלמנה",
                            "father": "בת %%father_name%%##lb##אלמנה",
                            "mother": "בת %%mother_name%%##lb##אלמנה",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%##lb##אלמנה",
                            "father_children": "בת %%father_name%%,  אלמנ##lb## אם ל-%%number_of_kids%%",
                            "mother_children": "בת %%mother_name%%,  אלמנ##lb## אם ל-%%number_of_kids%%",
                            "father_mother_children": "בת %%father_name%% ו%%mother_name%%,  אלמנ##lb## אם ל-%%number_of_kids%%",
                            "father_spouse": "בת %%father_name%%##lb##התאלמנה מ%%partner_name%%",
                            "mother_spouse": "בת %%mother_name%%##lb##התאלמנה מ%%partner_name%%",
                            "father_mother_spouse": "בת %%father_name%% ו%%mother_name%%##lb##התאלמנה מ%%partner_name%%",
                            "father_spouse_children": "בת %%father_name%%##lb##התאלמנה מ%%partner_name%%, אם ל-%%number_of_kids%%",
                            "mother_spouse_children": "בת %%mother_name%%##lb##התאלמנה מ%%partner_name%%, אם ל-%%number_of_kids%%",
                            "father_mother_spouse_children": "בת %%father_name%% ו%%mother_name%%##lb##התאלמנה מ%%partner_name%%, אם ל-%%number_of_kids%%",
                            "spouse": "התאלמנה מ%%partner_name%% ",
                            "spouse_children": "התאלמנה מ%%partner_name%%, אם ל-%%number_of_kids%%",
                            "children": "אלמנה, אם ל-%%number_of_kids%%"
                        },
                        "divorcee": {
                            "blank": "גרושה",
                            "father": "בת %%father_name%%##lb##גרושה",
                            "mother": "בת %%mother_name%%##lb##גרושה",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%##lb##גרושה",
                            "father_children": "בת %%father_name%%##lb##גרושה, אם ל-%%number_of_kids%%",
                            "mother_children": "בת %%mother_name%%##lb##גרושה, אם ל-%%number_of_kids%%",
                            "father_mother_children": "בת %%father_name%% ו%%mother_name%%##lb##גרושה, אם ל-%%number_of_kids%%",
                            "children": "גרושה, אם ל-%%number_of_kids%%"
                        },
                        "children_only": "אם ל-%%number_of_kids%% ילדים"
                    }
                },
                "summary": {
                    "city_sum": "נולד/התגורר ב%%city%%.",
                    "country_sum": "נולד/התגורר ב%%country%%.",
                    "city_country_sum": "נולד/התגורר ב%%city%%, %%country%%.",
                    "city_sum_female": "נולדה/התגוררה ב%%city%%.",
                    "country_sum_female": "נולדה/התגוררה ב%%country%%.",
                    "city_country_sum_female": "נולדה/התגוררה ב%%city%%, %%country%%.",
                    "occupation_sum": "%%occupation%%.",
                    "pod_sum": "%%place_of_death%%.",
                    "pod_sum_female": "%%place_of_death%%.",
                    "male_summary": {
                        "blank": {
                            "father": "בן %%father_name%%.",
                            "mother": "בו %%mother_name%%.",
                            "father_mother": "בן %%father_name%% ו%%mother_name%%."
                        },
                        "unknown": {
                            "blank": "",
                            "father": "בן %%father_name%%.",
                            "mother": "בן %%mother_name%%.",
                            "father_mother": "בן %%father_name%% ו%%mother_name%%.",
                            "father_children": "בן %%father_name%%, אב ל-%%number_of_kids%%.",
                            "mother_children": "בן %%mother_name%%, אב ל-%%number_of_kids%%.",
                            "father_mother_children": "בן %%father_name%% ו%%mother_name%%, אב ל-%%number_of_kids%%."
                        },
                        "adult_single": {
                            "blank": "רווק.",
                            "father": "בן %%father_name%%, רווק.",
                            "mother": "בן %%mother_name%%, רווק.",
                            "father_mother": "בן %%father_name%% ו%%mother_name%%, רווק."
                        },
                        "married": {
                            "blank": " נשוי.",
                            "father": "בן %%father_name%%,  נשוי.",
                            "mother": "בן %%mother_name%%,  נשוי.",
                            "father_mother": "בן %%father_name%% ו%%mother_name%%,  נשוי.",
                            "father_children": "בן %%father_name%%,  נשוי, אב ל-%%number_of_kids%%.",
                            "mother_children": "בן %%mother_name%%,  נשוי, אב ל-%%number_of_kids%%.",
                            "father_mother_children": "בן %%father_name%% ו%%mother_name%%,  נשוי, אב ל-%%number_of_kids%%.",
                            "father_spouse": "בן %%father_name%%, נשוי ל%%partner_name%%.",
                            "mother_spouse": "בן %%mother_name%%, נשוי ל%%partner_name%%.",
                            "father_mother_spouse": "בן %%father_name%% ו%%mother_name%%, נשוי ל%%partner_name%%.",
                            "father_spouse_children": "בן %%father_name%%, נשוי ל%%partner_name%%, אב ל-%%number_of_kids%%.",
                            "mother_spouse_children": "בן %%mother_name%%, נשוי ל%%partner_name%%, אב ל-%%number_of_kids%%.",
                            "father_mother_spouse_children": "בן %%father_name%% ו%%mother_name%%, נשוי ל%%partner_name%%, אב ל-%%number_of_kids%%.",
                            "spouse": "נשוי ל%%partner_name%%.",
                            "spouse_children": "נשוי ל%%partner_name%%, אב ל-%%number_of_kids%%.",
                            "children": "נשוי, אב ל-%%number_of_kids%% ילדים."
                        },
                        "widow": {
                            "blank": " אלמן.",
                            "father": "בן %%father_name%%,  אלמן.",
                            "mother": "בן %%mother_name%%,  אלמן.",
                            "father_mother": "בן %%father_name%% ו%%mother_name%%,  אלמן.",
                            "father_children": "בן %%father_name%%,  אלמן, אב ל-%%number_of_kids%%.",
                            "mother_children": "בן %%mother_name%%,  אלמן, אב ל-%%number_of_kids%%.",
                            "father_mother_children": "בן %%father_name%% ו%%mother_name%%,  התאלמן מל-%%number_of_kids%%.",
                            "father_spouse": "בן %%father_name%%, התאלמן מ%%partner_name%%.",
                            "mother_spouse": "בן %%mother_name%%, התאלמן מ%%partner_name%%.",
                            "father_mother_spouse": "בן %%father_name%% ו%%mother_name%%, התאלמן מ%%partner_name%%.",
                            "father_spouse_children": "בן %%father_name%%, התאלמן מ%%partner_name%%, אב ל-%%number_of_kids%%.",
                            "mother_spouse_children": "בן %%mother_name%%, התאלמן מ%%partner_name%%, אב ל-%%number_of_kids%%.",
                            "father_mother_spouse_children": "בן %%father_name%% ו%%mother_name%%, התאלמן מ%%partner_name%%, אב ל-%%number_of_kids%%.",
                            "spouse": "התאלמן מ%%partner_name%%.",
                            "spouse_children": "התאלמן מ%%partner_name%%, אב ל-%%number_of_kids%%.",
                            "children": "אלמן, אב ל-%%number_of_kids%% ילדים."
                        },
                        "divorcee": {
                            "blank": "גרוש.",
                            "father": "בן %%father_name%%,  גרוש.",
                            "mother": "בן %%mother_name%%,  גרוש.",
                            "father_mother": "בן %%mother_name%% %%father_name%%,  גרוש.",
                            "father_children": "בן %%father_name%%,  גרוש, אב ל-%%number_of_kids%%.",
                            "mother_children": "בן %%mother_name%%,  גרוש, אב ל-%%number_of_kids%%.",
                            "father_mother_children": "בן %%father_name%% ו%%mother_name%%,  גרוש, אב ל-%%number_of_kids%%.",
                            "children": "רוש, אב ל-%%number_of_kids%%."
                        },
                        "children_only": "אב ל-%%number_of_kids%% ילדים."
                    },
                    "female_summary": {
                        "blank": {
                            "father": "בת %%father_name%%.",
                            "mother": "בת %%mother_name%%.",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%."
                        },
                        "unknown": {
                            "blank": "",
                            "father": "בת %%father_name%%.",
                            "mother": "בת %%mother_name%%.",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%.",
                            "father_children": "בת %%father_name%%, אם ל-%%number_of_kids%%.",
                            "mother_children": "בת %%mother_name%%, אם ל-%%number_of_kids%%.",
                            "father_mother_children": "בת %%father_name%% ו%%mother_name%%, אם ל-%%number_of_kids%%."
                        },
                        "adult_single": {
                            "blank": "רווקה.",
                            "father": "בת %%father_name%%,  רווקה",
                            "mother": "בת %%mother_name%%,  רווקה.",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%,  רווקה."
                        },
                        "married": {
                            "blank": "נשואה.",
                            "father": "בת %%father_name%%,  נשואה.",
                            "mother": "בת %%mother_name%%,  נשואה.",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%,  נשואה.",
                            "father_children": "בת %%father_name%%,  נשואה, אם ל-%%number_of_kids%%.",
                            "mother_children": "בת %%mother_name%%,  נשואה, אם ל-%%number_of_kids%%.",
                            "father_mother_children": "בת %%father_name%% ו%%mother_name%%,  נשואה, אם ל-%%number_of_kids%%.",
                            "father_spouse": "בת %%father_name%%, נשואה ל%%partner_name%%.",
                            "mother_spouse": "בת %%mother_name%%, נשואה ל%%partner_name%%.",
                            "father_mother_spouse": "בת %%father_name%% ו%%mother_name%%, נשואה ל%%partner_name%%.",
                            "father_spouse_children": "בת %%father_name%%, נשואה ל%%partner_name%%, אם ל-%%number_of_kids%%.",
                            "mother_spouse_children": "בת %%mother_name%%, נשואה ל%%partner_name%%, אם ל-%%number_of_kids%%.",
                            "father_mother_spouse_children": "בת %%father_name%% ו%%mother_name%%, נשואה ל%%partner_name%%, אם ל-%%number_of_kids%%.",
                            "spouse": "נשואה ל%%partner_name%% .",
                            "spouse_children": "נשואה ל%%partner_name%%, אם ל-%%number_of_kids%%.",
                            "children": "נשואה, אם ל-%%number_of_kids%%."
                        },
                        "widow": {
                            "blank": "אלמנה.",
                            "father": "בת %%father_name%%,  אלמנה.",
                            "mother": "בת %%mother_name%%,  אלמנה.",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%,  אלמנה.",
                            "father_children": "בת %%father_name%%,  אלמנה, אם ל-%%number_of_kids%%.",
                            "mother_children": "בת %%mother_name%%,  אלמנה, אם ל-%%number_of_kids%%.",
                            "father_mother_children": "בת %%father_name%% ו%%mother_name%%,  אלמנה, אם ל-%%number_of_kids%%.",
                            "father_spouse": "בת %%father_name%%, התאלמנה מ%%partner_name%%.",
                            "mother_spouse": "בת %%mother_name%%, התאלמנה מ%%partner_name%%.",
                            "father_mother_spouse": "בת %%father_name%% ו%%mother_name%%, התאלמנה מ%%partner_name%%.",
                            "father_spouse_children": "בת %%father_name%%, התאלמנה מ%%partner_name%%, אם ל-%%number_of_kids%%.",
                            "mother_spouse_children": "בת %%mother_name%%, התאלמנה מ%%partner_name%%, אם ל-%%number_of_kids%%.",
                            "father_mother_spouse_children": "בת %%father_name%% ו%%mother_name%%, התאלמנה מ%%partner_name%%, אם ל-%%number_of_kids%%.",
                            "spouse": "התאלמנה מ%%partner_name%% .",
                            "spouse_children": "התאלמנה מ%%partner_name%%, אם ל-%%number_of_kids%%.",
                            "children": "אלמנה, אם ל-%%number_of_kids%%."
                        },
                        "divorcee": {
                            "blank": "גרושה.",
                            "father": "בת %%father_name%%,  גרושה.",
                            "mother": "בת %%mother_name%%,  גרושה.",
                            "father_mother": "בת %%father_name%% ו%%mother_name%%,  גרושה.",
                            "father_children": "בת %%father_name%%,  גרושה, אם ל-%%number_of_kids%%.",
                            "mother_children": "בת %%mother_name%%,  גרושה, אם ל-%%number_of_kids%%.",
                            "father_mother_children": "בת %%father_name%% ו%%mother_name%%,  גרושה, אם ל-%%number_of_kids%%.",
                            "children": "גרושה, אם ל-%%number_of_kids%%."
                        },
                        "children_only": "אם ל-%%number_of_kids%% ילדים."
                    }
                }
            }
        </script>
    </body>
</html>
