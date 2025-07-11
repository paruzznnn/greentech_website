

    <!-- ปุ่มต่างๆ จัดอยู่ด้านบน -->
    <div>
        <div class="nav nav-pills" id="prod-tab" style="width: 100%; margin-bottom: 5px;">
            <button class="nav-link active" data-target="#prod-t1" style="text-align: start;">
                <span data-key-lang="" lang="US">รายละเอียดเพิ่มเติม</span>
            </button>
            <!-- <button class="nav-link" data-target="#prod-t2" style="text-align: start;">
                <span data-key-lang="" lang="US">ยี่ห้อ</span>
            </button>
            <button class="nav-link" data-target="#prod-t3" style="text-align: start;">
                <span data-key-lang="" lang="US">ข้อมูลเพิ่มเติม</span>
            </button> -->
            <button class="nav-link" data-target="#prod-t4" style="text-align: start;">
                <span data-key-lang="" lang="US">บทวิจารณ์</span>
            </button>
        </div>
    </div>
    <hr>

    <!-- เนื้อหาของแท็บ -->
    <div class="tab-content" id="prod-tabContent" style="width: 100%; padding: 0 2%;">
        <div class="prod-tab-pane fade show active" id="prod-t1">
            <?php include 'template/product_info.php'?>
        </div>
        <!-- <div class="tab-pane fade" id="prod-t2">
            
        </div>
        <div class="tab-pane fade" id="prod-t3">
            
        </div> -->
        <div class="prod-tab-pane fade" id="prod-t4">
            <?php include 'template/review_detail.php'?>
        </div>

    </div>


