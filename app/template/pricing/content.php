<?php
$boxesPlug = [
    [
        'image' => '../public/img/expense.jpg',
        'title' => 'AI powered search',
        'label' => 'Free',
        'description' => 'The easiest way to get started with AI
        Embed GenAI powered Search across your website, help center, & mobile/desktop experience',
        'text-btn' => 'Get started - free forever',
        'items' => [
            'GenAI powered search', 
            'One simple SDK'
            ]
    ],
    [
        'image' => '../public/img/expense.jpg',
        'title' => 'Pay as you go',
        'label' => 'Flexible',
        'description' => 'AI + Engage + observe, better together
        Seamlessly add user engagement & observability with the same SDK integration',
        'text-btn' => 'Get started free',
        'items' => [
            'AI powered search & deflections', 
            'In-app Agents', 
            'Product and support observability',
            'Session replays'
            ]
    ],
    [
        'image' => '../public/img/expense.jpg',
        'title' => 'Ultimate',
        'label' => 'Let’s talk',
        'description' => 'Scale with PLuG’s most advanced functionality
        Understand your user wherever they are, with volume discounting',
        'text-btn' => 'Contact sales',
        'items' => [
            'Conversion drop insights', 
            'Advanced Session Filtering', 
            'Heat maps',
            'Enterprise-grade security, compliance,
            controls, and policies
            '
            ]
    ]
];

$boxesSupport = [
    [
        'image' => '../public/img/expense.jpg',
        'title' => 'Starter',
        'label' => '679.66 THB <br>
        per user/month',
        'description' => 'The easiest way to get started with AI
        Embed GenAI powered Search across your website, help center, & mobile/desktop experience',
        'text-btn' => 'Get started - free forever',
        'items' => [
            'AI agents, assistants, and deflection', 
            'Modern omnichannel ticketing platform', 
            'Data migration and integrations',
            'Ready to go reporting and analytics'
            ]
    ],
    [
        'image' => '../public/img/expense.jpg',
        'title' => 'Pro',
        'label' => '2,039.66 THB <br>
        per user/month',
        'description' => 'Full featured support and engagement platform for scaling support orgs',
        'text-btn' => 'Get started free',
        'items' => [
            'Everything in Starter', 
            'Advanced reporting & analytics', 
            'Custom SLA and routing policies',
            'Customizable object and data types'
            ]
    ],
    [
        'image' => '../public/img/expense.jpg',
        'title' => 'Ultimate',
        'label' => 'Let’s talk',
        'description' => 'Enterprise scale and flexibility to meet any complex requirement',
        'text-btn' => 'Contact sales',
        'items' => [
            'Everything in Pro', 
            'Full object model customization and
            unlimited integrations
            ', 
            'Enterprise-grade security, compliance,
            controls, and policies
            '
            ]
    ]
];


$boxesBuild = [
    [
        'image' => '../public/img/expense.jpg',
        'title' => 'Starter',
        'label' => '339.66 THB <br>
        per user/month',
        'description' => 'For startups and small engineering teams',
        'text-btn' => 'Get started - free forever',
        'items' => [
            'AI agents, assistants, and issue tracking', 
            'Sprint management', 
            'Roadmapping and dependency tracking',
            'Ready to go reporting and analytics'
            ]
    ],
    [
        'image' => '../public/img/expense.jpg',
        'title' => 'Pro',
        'label' => '849.66 THB <br>
        per user/month',
        'description' => 'Full featured product development platform for scaling teams',
        'text-btn' => 'Get started free',
        'items' => [
            'Everything in Starter', 
            'Customizable Issue management', 
            'Advanced reporting & analytics'
            ]
    ],
    [
        'image' => '../public/img/expense.jpg',
        'title' => 'Ultimate',
        'label' => 'Let’s talk',
        'description' => 'Enterprise scale and flexibility to meet any complex requirement',
        'text-btn' => 'Contact sales',
        'items' => [
            'Everything in Pro', 
            'Full object model customization and
            unlimited integrations
            ', 
            'Enterprise-grade security, compliance, controls, and policies'
            ]
    ]
];

?>


<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-plug">

        <div class="content-pricing">
            <?php foreach ($boxesPlug as $index => $box): ?>
                <div class="box-pricing">
                    <div>
                        <p><?php echo $box['title']; ?></p>
                    </div>
                    <div>
                        <h6><?php echo $box['label']; ?></h6>
                        <p class="line-clamp"><?php echo $box['description']; ?></p>
                    </div>
                    <div>
                        <ul>
                            <?php 
                            
                            if (isset($box['items'])): 
                                foreach ($box['items'] as $item): ?>
                                    <li><?php echo $item; ?></li>
                                <?php endforeach; 
                            else: ?>
                                <!-- <li>No items available</li>  -->
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        </div>

    </div>
    <div class="tab-pane fade" id="tab-support">

        <div class="content-pricing">
        <?php foreach ($boxesSupport as $index => $box): ?>
            <div class="box-pricing">
                <div>
                    <p><?php echo $box['title']; ?></p>
                </div>
                <div>
                    <h6><?php echo $box['label']; ?></h6>
                    <p class="line-clamp"><?php echo $box['description']; ?></p>
                </div>
                <div>
                    <ul>
                        <?php 
                        
                        if (isset($box['items'])): 
                            foreach ($box['items'] as $item): ?>
                                <li><?php echo $item; ?></li>
                            <?php endforeach; 
                        else: ?>
                            <!-- <li>No items available</li>  -->
                        <?php endif; ?>
                    </ul>
                </div>
                
            </div>
        <?php endforeach; ?>
    </div>
        
    </div>
    <div class="tab-pane fade" id="tab-build">

        <div class="content-pricing">
        <?php foreach ($boxesBuild as $index => $box): ?>
            <div class="box-pricing">
                <div>
                    <p><?php echo $box['title']; ?></p>
                </div>
                <div>
                    <h6><?php echo $box['label']; ?></h6>
                    <p class="line-clamp"><?php echo $box['description']; ?></p>
                </div>
                <div>
                    <ul>
                        <?php 
                        
                        if (isset($box['items'])): 
                            foreach ($box['items'] as $item): ?>
                                <li><?php echo $item; ?></li>
                            <?php endforeach; 
                        else: ?>
                            <!-- <li>No items available</li>  -->
                        <?php endif; ?>
                    </ul>
                </div>
                
            </div>
        <?php endforeach; ?>
        </div>
        
    </div>
</div>
