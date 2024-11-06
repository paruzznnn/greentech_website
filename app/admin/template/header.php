<?php
global $base_path_admin;
global $base_path;
global $public_path;

echo '<script>
    window.base_path_admin = "' . $base_path_admin . '";
    window.base_path = "' . $base_path . '";
    window.public_path = "' . $public_path . '";
</script>';

?>


<div class="header-top">
    <div class="container">


        <div class="header-top-left">
            <a href="#">
                <img class="logo" src="<?php echo $public_path ?>/img/logo-ALLABLE-06.png" alt="">
            </a>
        </div>
            

        <div class="header-top-right">
                <div>
                    <span class="toggle-button">
                        <i id="toggleIcon" class="fas fa-bars"></i>
                    </span>
                </div>
                
            <!-- <div>
                <select id="language-select" class="language-select">
                </select>
            </div> -->

        </div>

    </div>
</div>

<div id="showTabSidebar" class="row row-tab"></div>
