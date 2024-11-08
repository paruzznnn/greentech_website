<?php
$boxesNews = [
    [
        'image' => '../public/img/HARMONY-THAI-BUILDING-FAIR6.2-01-1536x1086.jpg',
        'title' => 'AI powered search',
        'label' => 'Free',
        'description' => 'The easiest way to get started with AI
        Embed GenAI powered Search across your website, help center, & mobile/desktop experience'
    ],
    [
        'image' => '../public/img/Trandar-Harmony-Greenday-1-1536x1024.jpg',
        'title' => 'Pay as you go',
        'label' => 'Flexible',
        'description' => 'AI + Engage + observe, better together
        Seamlessly add user engagement & observability with the same SDK integration'
    ],
    [
        'image' => '../public/img/Trandar-Acoustics-_ระบบผนัง-Trandar-Hitech-Wall-1-1536x1536.jpg',
        'title' => 'Ultimate',
        'label' => 'Let’s talk',
        'description' => 'Scale with PLuG’s most advanced functionality
        Understand your user wherever they are, with volume discounting'
    ]
];


?>
<div class="content-news">
    <?php foreach ($boxesNews as $index => $box): ?>
        <div class="box-news">
            <div class="box-image">
                <img src="<?php echo $box['image']; ?>" alt="Image for <?php echo $box['title']; ?>">
            </div>
            <div class="box-content">
                <p><?php echo $box['title']; ?></p>
                <h6><?php echo $box['label']; ?></h6>
                <p class="line-clamp"><?php echo $box['description']; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>



