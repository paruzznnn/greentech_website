<?php

?>
<!DOCTYPE html>
<html>
<head>


    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
    
</head>
<body>

    <?php include 'template/header.php'?>
    <?php include 'template/navbar_slide.php'?>

    <?php
        
        // Array of content sections
        $contentSections = [
            [
                'text' => '<h2 style="font-size: 28px; font-weight: bold;">INSUL Acoustics Software: The Ultimate Standard for Acoustic Services</h2>'
            ],
            [
                'text' => 'Based on our experience in Research and Development and as expert acoustic consultants, Trandar Acoustics has found that even using high-quality materials for installation or construction doesn\'t always meet the efficiency that clients truly need.'
            ],
            [
                'text' => 'This is why Trandar Acoustics introduced a software service: the INSUL program. It allows clients to understand the original sound levels and the acoustic values that can be achieved, helping to prevent costly mistakes in material selection.'
            ],
            [
                'image' => '../public/img/acoustics.jpg'
                // 'text' => '<h2 style="font-size: 28px; font-weight: bold;">Company Objective</h2>'
            ],
            [
                'text' => 'INSUL is an acoustic simulation software that is convenient, fast, and accurate for calculating various values. It is mostly used to predict the sound insulation performance of wall systems and can effectively define the properties of different materials. It is a program that calculates the sound insulation of walls, floors, ceilings, and windows. It displays the loss values in 1/3 OCTAVE BANDS frequencies and the Weighted Sound Reduction Index (STC or Rw). For sound insulation calculations, materials or designs can be changed easily. It is convenient, fast, and accurate in calculating various values.'
            ],
            [
                'text' => 'Today, INSUL is considered a popular software among all manufacturers of gypsum boards, ceiling panels, and walls, who have chosen to use it. Let\'s talk about how the INSUL program can benefit you!'
            ],
            [
                'text' => '<h2 style="font-size: 28px; font-weight: bold;">5 Excellent Benefits of the INSUL Program for Acoustic Applications</h2>'
            ],
            [
                'text' => '1. Creates a list of transmission loss for airborne and rain sound.'
            ],
            [
                'text' => 'INSUL is a program for calculating the sound insulation of walls, floors, ceilings, windows, as well as impact and rain sound on floors and roofs. The program can calculate values known as Transmission Loss (TL) or Impact Sound (Ln) in 1/3 octave bands and display comparative values such as STC or Rw or Impact Rating (IIC / LnTw) to analyze the quality of the sound insulation system regarding noise, rain noise, sunbathing, or sound regulations.'
            ],
            [
                'image' => '../public/img/insul1.jpg'
            ],
            [
                'text' => '<p style="border-top: 2px; padding-top: 12px; margin-top: 24px;">2. Evaluate material or design changes</p>'

            ],
            [
                'text' => 'INSUL can be used to quickly evaluate new materials and systems or check the impact of changes in existing designs as a material model. It uses graphs for well-known flexible sheets, as well as values suitable for thick panel effects as published by Ljunggren, Rindell, and others. More complex parameters are simulated using the work of Sharp, Cremer, and others.'
            ],
            [
                'text' => '3. Continuous Development'
            ],
            [
                'text' => 'INSUL has been available for over 15 years and has been significantly improved during this time. It has been developed in several versions and has become an easy-to-use and useful tool that supports both Windows and Mac systems. It has also been continuously improved by comparing it with laboratory test data to achieve acceptable accuracy for a wide variety of real projects.'
            ],
            [
                'text' => 'Test data can be easily entered for comparison with predictions, and structures can be saved to be recalled later.'
            ],
            [
                'image' => '../public/img/insul2.jpg'
            ],
            [
                'text' => '<p style="border-top: 2px; padding-top: 12px; margin-top: 24px;">4. Specify constructions with speed and confidence</p>'
            ],
            [
                'text' => 'INSUL takes into account the effects of size and spatial limitations, which are very important when making predictions for small areas, such as windows, and also for typical low-frequency components.'
            ],
            [
                'text' => 'However, INSUL cannot be used for measurement. Nevertheless, in comparison to test data, it indicates that INSUL can reliably predict STC values within 3dB in large-scale constructions. This helps to enhance the ability of acoustic design teams and product manufacturers to quickly and confidently specify constructions for use in required documentation.'
            ],
            [
                'text' => '5. Available in multiple languages'
            ],
            [
                'text' => 'INSUL can be changed to display information in English, French, German, Polish, Spanish, Swedish, or other languages. The translations are done by acoustic specialists to be as suitable as possible for the technical terminology commonly used in each country.'
            ],
            [
                'text' => 'Noise is a significant problem that often occurs everywhere, whether in office buildings, residential areas, or even in industrial factories. It is a risk that has a very negative impact on work. Especially in industrial factories, where it is the noisiest workplace, some machinery operations can be dangerous to employees if there are no regulatory measures in place.'
            ],
            [
                'text' => 'If you want software that is easy to understand, easy to use, and effective, Trandar Acoustics offers the INSUL Acoustics Software service to provide you with peace of mind, without worry, and without having to fix or create new structures in the future.'
            ],
            [
                'text' => '<p style="border-top: 2px; padding-top: 12px; margin-top: 24px;">Image source: Insul</p>'
            ],
        ];

        // Initialize content
        $content = '<div class="content-sticky" id="page_about">';
        $content .= '<div class="container" style="max-width: 90%;">';
        $content .= '<div class="box-content">';

        // Loop through each section
        foreach ($contentSections as $section) {
    $content .= '<div class="row">';

    // If there's an image
    if (isset($section['image'])) {
        $content .= '<div class="col-md-6">';
        $content .= '<img style="width: 100%;" src="' . $section['image'] . '" alt="">';
        $content .= '</div>';
    }

    // If there are multiple text blocks (structure/policy/vision)
    if (isset($section['texts']) && is_array($section['texts'])) {
        $content .= '<div class="col-md-' . (isset($section['image']) ? '6' : '12') . '">';
        $content .= '<div class="d-flex justify-content-between">';
        foreach ($section['texts'] as $text) {
            $content .= '<div style="width: 32%; padding: 0 10px;">' . $text . '</div>';
        }
        $content .= '</div></div>';
        $content .= '<div class="col-12"><hr></div>';
    }

    // If there's a quote
    if (isset($section['quote'])) {
        $quote = $section['quote'];
        $content .= '
        <div style="text-align: center; padding: 40px 20px; font-style: italic; font-size: 25px; position: relative; width: 100%;">
            <div style="font-size: 40px; color: #ccc; position: absolute; left: 10px; top: 0;">“</div>
            <p style="margin: 0 40px;">' . $quote['text'] . '</p>
            <div style="margin-top: 20px; font-style: normal;">
                <strong>' . $quote['author'] . '</strong><br>' . $quote['position'] . '
            </div>
        </div>';
        // $content .= '<div class="col-12"><hr></div>';
    }

    // If there's a single text block
    if (isset($section['text']) && !isset($section['texts'])) {
        $content .= '<div class="col-md-' . (isset($section['image']) ? '6' : '12') . '">';
        $content .= '<p>' . $section['text'] . '</p>';
        $content .= '</div>';
    }

    $content .= '</div>'; // close row

    if (isset($section['image'])) {
        // $content .= '<hr>';
    }
}


        $content .= '</div>'; // Close box-content
        $content .= '</div>'; // Close container
        $content .= '</div>'; // Close content-sticky

        // Output the content
        echo $content;
    ?>

    <?php include 'template/footer.php'?>

    <script src="js/index_.js?v=<?php echo time();?>"></script>

</body>
</html>