
<div class="container-fluid mt-3 mb-4">

    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-light bg-white card proviewcard shadow-sm">
                    <!-- <div class="card-header"></div> -->

                    <div class="card-body loadScroll" style="height: 280px !important; overflow: auto !important;">
                        <div id="box-review"></div>
                    </div>

                    <div class="card-footer border-light cart-panel-foo-fix">

                        <div class="">
                            <input type="text" id="memberReview" value="<?php echo $_SESSION['user_id'] ?? '';?>" hidden>
                            <textarea class="form-control" id="textReview" rows="3"></textarea>
                        </div>
                        
                        <br>
                        <button type="button" id="sendReview" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i><span data-key-lang="" lang="US"> ส่ง</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>