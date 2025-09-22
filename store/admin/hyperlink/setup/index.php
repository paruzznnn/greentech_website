<?php include '../../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../../inc-meta.php'; ?>
    <link href="../../../css/admin/template-admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../../inc-cdn.php'; ?>
    <link href="../../../css/admin/template-notify.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../../css/admin/template-dataTable.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        .card-hyperlink {
            border: 1px solid #dee2e6;
            background: #ffffff;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-hyperlink-section {
            border: 2px dashed #dee2e6;
            background: #ffffff;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .box-icon-picker {
            position: relative;
        }

        .iconPicker {
            position: absolute;
            right: 0;
            background-color: #fafafa;
            top: 0px;
            z-index: 99;
            border: 1px solid #6b7280;
            border-radius: 3px;
            padding: 5px;
        }
    </style>
</head>

<body>

    <?php include '../../../template/admin/head-bar.php'; ?>
    <main>
        <div id="section_root_hyperlink_setup" class="section-space-admin">
            <div class="container">
                <section>
                    <form id="setupFormLink" data-url="<?php echo $BASE_WEB ?>service/admin/control/setup-link-action.php" data-redir="<?php echo $BASE_WEB ?>admin/hyperlink/" data-type="setupLink">
                        <?php
                        if (isset($_GET['id'])) { ?>
                            <input type="text" name="action" value="editSetupLink" hidden>
                            <input type="text" name="link_id" value="<?php echo $_GET['id'] ?>" hidden>
                        <?php } else { ?>
                            <input type="text" name="action" value="setupAddLink" hidden>
                        <?php } ?>
                        <div class="card-hyperlink">
                            <h5>ส่วนตั้งค่า</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex">
                                            <label for="link_icon" class="form-label">ไอคอน:</label>
                                            <i id="showIcon" class=""></i>
                                        </div>
                                        <div class="input-wrapper">
                                            <input type="text" id="link_icon" name="link_icon"
                                                class="form-input" required>
                                            <button type="button" id="targetIconPicker" class="toggle-btn">
                                                <i class="fas fa-table"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="box-icon-picker">
                                        <div class="iconPicker"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link_label" class="form-label">ชื่อเมนู</label>
                                        <input type="text" id="link_label" name="link_label" class="form-input">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="link_path" class="form-label">เส้นทาง (Path)</label>
                                        <input type="text" id="link_path" name="link_path" class="form-input">
                                    </div>
                                </div>
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link_role" class="form-label">ให้สิทธิเมนูส่วน</label>
                                        <select id="link_role" name="link_role" class="form-input" required>
                                            <option value="">-- เลือกสิทธิ --</option>
                                            <option value="1">แอดมิน</option>
                                            <option value="2">ผู้ใช้งาน</option>
                                            <option value="3">ผู้เยี่ยมชม</option>
                                        </select>
                                    </div>
                                </div> -->
                                <!-- <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="link_comp" class="form-label">ประเภทบริษัท</label>
                                        <select id="link_comp" name="link_comp" class="form-input" required>
                                            <option value="">-- เลือกประเภทบริษัท --</option>
                                            <option value="1">วัสดุก่อสร้าง</option>
                                            <option value="2">ซอฟต์แวร์</option>
                                            <option value="3">อาหาร</option>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="col-md-12">
                                    <div class="d-flex flex-row-reverse mb-2" style="gap: 1rem">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="open_type" name="open_type"/>
                                            <span class="slider"></span>
                                        </label>
                                        <span>ต้องการเปิดใช้งานส่วนเสริมหรือไม่</span>
                                    </div>
                                </div>
                                <div class="col-md-12" id="display_type" style="display: none;">
                                    <div class="form-group">
                                        <label for="link_type" class="form-label">ส่วนเสริม</label>
                                        <select id="link_type" class="form-input">
                                            <option value="">-- เลือกส่วนเสริม --</option>
                                            <option value="image">รูปภาพ</option>
                                            <option value="menu">เมนู</option>
                                            <option value="text">ข้อความ</option>
                                        </select>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-between">
                                        <span id="limit-message"> สามารถเพิ่มได้สูงสุด 4 ส่วนเท่านั้น</span>
                                        <button type="button" class="btn btn-primary btn-sm" id="add-section-btn">เพิ่ม</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="dynamic-content-container">
                        </div>
                        <div style="text-align: end;">
                            <button type="submit" class="btn w-100" style="background-color: #FF9800;">
                                บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
    <?php include '../../../template/admin/footer-bar.php'; ?>

    <script type="module">
        Promise.all([
                import(`${pathConfig.BASE_WEB}js/centerHandler.js?v=<?php echo time(); ?>`),
                import(`${pathConfig.BASE_WEB}js/admin/control/linkBuilder.js?v=<?php echo time(); ?>`)
            ])
            .then(async ([formModule, linkModule]) => {
                const {
                    handleFormSubmit,
                    showMessageBox
                } = formModule;
                const {
                    DynamicSectionManager,
                    fetchLinkData
                } = linkModule;

                // ============ ICON PICKER ============
                const iconInput = document.getElementById("link_icon");
                const showIcon = document.getElementById("showIcon");

                $(".iconPicker")
                    .addClass("d-none")
                    .iconpicker({
                        iconset: "fontawesome5",
                        selectedClass: "btn-link",
                        unselectedClass: "btn-light"
                    })
                    .on("change", e => {
                        iconInput.value = e.icon;
                        showIcon.className = e.icon;
                    });

                $(document).on("click", e => {
                    if (!$(e.target).closest(".iconPicker").length && !$(e.target).is("#targetIconPicker")) {
                        $(".iconPicker").addClass("d-none");
                    }
                });

                $("#targetIconPicker").on("click", e => {
                    e.stopPropagation();
                    $(".iconPicker").toggleClass("d-none");
                });

                const openType = document.getElementById("open_type");
                const displayType = document.getElementById("display_type");
                openType.addEventListener("change", function () {
                    if (openType.checked) {
                    displayType.style.display = "block";
                    } else {
                    displayType.style.display = "none";
                    }
                });

                // ============ MAIN FORM ============
                const linkId = <?php echo isset($_GET['id']) ? (int)$_GET['id'] : 0; ?>;
                let links = {
                    data: {}
                };

                if (linkId) {
                    const service = pathConfig.BASE_WEB + 'service/admin/control/list-link-data.php?';
                    const param = {
                        action: "getLinkItems",
                        id: linkId
                    };
                    links = await fetchLinkData(param, service);

                    const existing_data = links.data?.main[0];
                    if(existing_data){
                        document.getElementById("link_icon").value = existing_data.link_icon;
                        document.getElementById("showIcon").className = existing_data.link_icon;
                        document.getElementById("link_label").value = existing_data.link_name;
                        document.getElementById("link_path").value = existing_data.link_url;
                    }

                }

                DynamicSectionManager.init({
                    sectionLimit: 4,
                    typeSelectorId: "link_type",
                    addButtonId: "add-section-btn",
                    containerId: "dynamic-content-container",
                    limitMessageId: "limit-message"
                });

                // ============ AUTO LOOP FOR SECTIONS ============
                const sectionHandlers = {
                    image: (items) => items.map(item => ({
                        id: item.id || 0,
                        url: item.link_sub_img,
                        category: item.link_sub_category || "",
                        status: "existing"
                    })),

                    menu: (items) => items.map(item => ({
                        id: item.id || 0,
                        label: item.link_sub_name,
                        href: item.link_sub_url,
                        status: "existing"
                    })),

                    text: (items) => {
                        // text section เก็บเป็น 1 record ต่อ sort
                        const first = items[0] || {};
                        return {
                            id: first.id || 0,
                            text: first.link_sub_text || "",
                            status: "existing"
                        };
                    }
                };

                // ฟังก์ชันช่วย group ตาม link_sub_sort
                function groupBySort(items) {
                    return items.reduce((acc, item) => {
                        const sort = item.link_sub_sort ?? 0;
                        if (!acc[sort]) acc[sort] = [];
                        acc[sort].push(item);
                        return acc;
                    }, {});
                }

                // Loop ทุก type
                Object.keys(sectionHandlers).forEach(type => {
                    const items = links.data?.[type];
                    if (Array.isArray(items) && items.length > 0) {
                        // group ก่อน
                        const grouped = groupBySort(items);

                        Object.values(grouped).forEach(group => {
                            const formatted = sectionHandlers[type](group);
                            DynamicSectionManager.addSection(type, formatted, true); // isFromDB = true
                        });
                    } else {
                        console.warn(`No data found for section: ${type}`);
                    }
                });


                // ============ FORM SUBMIT ============
                const formSetup = document.querySelector("#setupFormLink");
                formSetup?.addEventListener("submit", handleFormSubmit);
                
            })
            .catch((e) => console.error("Module import failed", e));

        // showMessageBox("คุณต้องการบันทึกข้อมูลหรือไม่?", 
        //     () => console.log("ข้อมูลที่บันทึก"), 
        //     () => console.log("กดยกเลิก")
        // );
    </script>

</body>

</html>