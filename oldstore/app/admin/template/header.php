<?php
require_once(__DIR__ . '/../../../lib/base_directory.php');
$basePath = $base_path_admin;
?>
<div id="loading-overlay" class="hidden">
    <div class="spinner"></div>
</div>


<div id="myModal-setup-menu" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close-setup-menu">&times;</span>
            <h6 style="text-align: center; position: absolute; left: 40%;" class="mt-2"><i class="fas fa-sliders-h"></i> Setup menu</h6>
        </div>

            <div class="modal-body" style="background-color: #9e9e9e1f;">
                <div class="row">
                    <div class="col-md-6">
                        <form id="formSetMenu">
                            <div style="padding: 40px 2px;">
                                <!-- Hidden input for icon selection -->
                                <input type="text" id="set_icon" name="set_icon" class="form-control" value="" hidden>

                                <!-- Menu Name Input -->
                                <div class="form-group">
                                    <label for="munu_name">Menu</label>
                                    <input type="text" id="munu_name" name="munu_name" class="form-control" value="" required>
                                </div>

                                <!-- Menu Path Input -->
                                <div class="form-group">
                                    <label for="munu_path">Path</label>
                                    <input type="text" id="munu_path" name="munu_path" class="form-control" value="" required>
                                </div>

                                <!-- Menu Main Input -->
                                <div class="form-group">
                                    <label for="menu_main" class="form-label">Main</label>
                                    <select id="menu_main" name="menu_main" class="form-select" style="width: 100%;"></select>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6">
                        <div style="padding: 10px; margin-top: 10px;">
                            <div class="iconPicker"></div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="submitFormSetMenu" class="btn btn-success"><i class="fas fa-plus"></i> Add</button>
            </div>

    </div>

</div>

<div id="myModal-setup-language" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close-setup-language">&times;</span>
            <h6 style="text-align: center; position: absolute; left: 40%;" class="mt-2"><i class="fas fa-flag"></i> Setup languages</h6>
        </div>

            <div class="modal-body" style="background-color: #9e9e9e1f;">
                <div class="row">
                    <div class="col-md-12">

                        <form id="formSetLanguage">
                            <input type="hidden" id="action" name="action" value="add">
                            <input type="hidden" id="id" name="id"> 

                            <div class="form-group mb-3">
                                <label for="language_name">language</label>
                                <select id="language_name" name="language_name" class="form-select" style="width: 100%;" required>
                                    <option value="en">EN</option>
                                    <option value="th">TH</option>
                                    <option value="cn">CN</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="language_key">Key word</label>
                                <input type="text" id="language_key" name="language_key" class="form-control" value="" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="language_word">Translation</label>
                                <input type="text" id="language_word" name="language_word" class="form-control" value="" required>
                            </div>

                            <div style="text-align: end;">
                                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="response"></div>
            </div>

    </div>

</div>

<div id="myModal-setup-shipping" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close-setup-shipping">&times;</span>
            <h6 style="text-align: center; position: absolute; left: 40%;" class="mt-2"><i class="fas fa-truck"></i> Setup shipping</h6>
        </div>

            <div class="modal-body" style="background-color: #9e9e9e1f;">
                <div class="row">
                    <div class="col-md-12">
                        <!-- <form id="formSetMenu">
                            <div style="padding: 40px 2px;">

                                <input type="text" id="set_icon" name="set_icon" class="form-control" value="" hidden>

                                <div class="form-group">
                                    <label for="munu_name">Menu</label>
                                    <input type="text" id="munu_name" name="munu_name" class="form-control" value="" required>
                                </div>

                                <div class="form-group">
                                    <label for="munu_path">Path</label>
                                    <input type="text" id="munu_path" name="munu_path" class="form-control" value="" required>
                                </div>

                                <div class="form-group">
                                    <label for="menu_main" class="form-label">Main</label>
                                    <select id="menu_main" name="menu_main" class="form-select" style="width: 100%;"></select>
                                </div>
                            </div>
                        </form> -->
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="submitFormSetMenu" class="btn btn-success"><i class="fas fa-plus"></i> Add</button>
            </div>

    </div>

</div>


<div class="row">
        <div class="head-store">
            <div class="display-in-head">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <div style="display: flex; flex-direction: row-reverse; align-items: center;">
                        <div style="text-align: center;">
                            <span id="pageHome">
                                <img style="width: 50%;" src="" alt="Logo">
                            </span>
                        </div>
                        <div>
                            <span class="toggle-button">
                                <i id="toggleIcon" class="fas fa-bars"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6">
                    
                </div>
                <div class="col-md-4 col-sm-4 col-xs-4" style="text-align: center;">
                    
                    <span class="display-info">
                        <a href="#" class="display-link">
                            <i class="fas fa-bell"></i>
                        </a>
                    </span>
                    

                    <span class="display-info">
                        <a href="<?php echo $basePath . 'logout.php'; ?>" class="display-link">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </span>
                    
                    <span>
                        <select class="form-element">
                            <option value="1" selected>EN</option>
                        </select>
                    </span>

                </div>
            </div>
        </div>
</div>



<div id="showTabSidebar" class="row row-tab"></div>


