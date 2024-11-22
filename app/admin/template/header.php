

<div id="loading-overlay" class="hidden">
    <div class="spinner"></div>
</div>


<div class="header-top">
    <div class="container-fluid">

        <div class="header-top-left">
            <div>
                <span class="toggle-button">
                    <i id="toggleIcon" class="fas fa-bars"></i>
                </span>
            </div>
            <div>
                <a href="#">
                    <!-- <img src="#" alt="" class="logo"> -->
                </a>
            </div>
        </div>
            
        <div class="header-top-right">

            <div class="header-item">
                <i class="fas fa-bell"></i>
            </div>

            <div>
                <select id="language-select" class="language-select">
                </select>
            </div>    

            <div class="profile-container">
                <img src="#" alt="" class="profile-pic">
            </div>

            <div class="dropdown">
                <button class="dropdown-btn">
                    <i class="fas fa-caret-up"></i>
                </button>
            </div>

        </div>

    </div>
</div>
<div class="dropdown-content">

    <a href="#">Profile</a>
    <a href="<?php echo $base_path_admin.'logout.php' ?>">Log out</a>

</div>

<div id="showTabSidebar" class="row row-tab"></div>
