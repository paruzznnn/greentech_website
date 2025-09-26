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
    <style>
        .card-product-setup {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
        }
    </style>
</head>

<body>

    <?php include '../../../template/admin/head-bar.php'; ?>
    <main>
        <div id="section_root_product_setup" class="section-space-admin">
            <div class="container">
                <form action="#" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <section>
                                <div class="card-product-setup">
                                    <h5>ข้อมูลพื้นฐาน</h5>
                                    <div class="form-group mb-3">
                                        <label for="product_name" class="form-label">ชื่อสินค้า:</label>
                                        <input type="text" id="product_name" name="product_name" class="form-input form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="manufacturer" class="form-label">ชื่อผู้ผลิต:</label>
                                        <input type="text" id="manufacturer" name="manufacturer" class="form-input form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="product_id" class="form-label">หมายเลขประจำตัวผลิตภัณฑ์:</label>
                                        <input type="text" id="product_id" name="product_id" class="form-input form-control" required>
                                    </div>
                                </div>
                            </section>
                            <section class="mt-4">
                                <div class="card-product-setup">
                                    <h5>เพิ่มรูปภาพ</h5>
                                    <label for="input-24">ภาพสินค้า</label>
                                    <div class="file-loading">
                                        <input type="file" id="input-24" name="images[]" class="file" data-show-preview="false" multiple>
                                    </div>
                                </div>
                            </section>
                            <section class="mt-4">
                                <div class="card-product-setup">
                                    <h5>รายละเอียดสินค้า:</h5>
                                    <textarea name="product_description" rows="5" class="form-control"></textarea>
                                </div>
                            </section>
                            <section class="mt-4">
                                <div class="card-product-setup">
                                    <h5>ข้อมูลจำเพาะ:</h5>
                                    <textarea name="product_specification" rows="5" class="form-control"></textarea>
                                </div>
                            </section>
                        </div>
                        <div class="col-md-4">
                            <aside class="mt-4">
                                <div class="card-product-setup">
                                    <h5>พิมพ์</h5>
                                    <input type="text" name="product_type" class="form-control">
                                </div>
                            </aside>
                            <aside class="mt-4">
                                <div class="card-product-setup">
                                    <h5>แท็ก</h5>
                                    <input type="text" name="product_tags" class="form-control" placeholder="ใส่แท็ก คั่นด้วย comma">
                                </div>
                            </aside>
                            <aside class="mt-4">
                                <div class="card-product-setup">
                                    <h5>การกำหนดราคา</h5>
                                    <input type="number" name="product_price" class="form-control" min="0" step="0.01">
                                </div>
                            </aside>
                            <aside class="mt-4">
                                <div class="card-product-setup">
                                    <h5>สถานะสต๊อก</h5>
                                    <select name="stock_status" class="form-select">
                                        <option value="in_stock">มีสินค้า</option>
                                        <option value="out_of_stock">หมดสินค้า</option>
                                        <option value="pre_order">สั่งจองล่วงหน้า</option>
                                    </select>
                                </div>
                            </aside>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary">บันทึกสินค้า</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php include '../../../template/admin/footer-bar.php'; ?>


    <script>
        $(document).ready(function() {
            var url1 = 'http://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/FullMoon2010.jpg/631px-FullMoon2010.jpg',
                url2 = 'http://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Earth_Eastern_Hemisphere.jpg/600px-Earth_Eastern_Hemisphere.jpg';
            $("#input-24").fileinput({
                initialPreview: [url1, url2],
                initialPreviewAsData: true,
                initialPreviewConfig: [
                    {caption: "Moon.jpg", downloadUrl: url1, description: "<h5>The Moon</h5>The Moon is Earth's only natural satellite and the fifth largest moon in the solar system. The Moon's distance from Earth is about 240,000 miles (385,000 km).", size: 930321, width: "120px", key: 1},
                    {caption: "Earth.jpg", downloadUrl: url2, description: "<h5>The Earth</h5> The Earth is the 3<sup>rd</sup> planet from the Sun and the only astronomical object known to harbor and support life. About 29.2% of Earth's surface is land and remaining 70.8% is covered with water.", size: 1218822, width: "120px", key: 2}
                ],
                deleteUrl: "/site/file-delete",
                overwriteInitial: false,
                maxFileSize: 100,
                initialCaption: "The Moon and the Earth"
            });
        });
    </script>

</body>

</html>