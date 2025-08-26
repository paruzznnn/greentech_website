<?php include '../check_permission.php'?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Users</title>
    
    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">
    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>
    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../../inc/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>
    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>
    <?php include '../template/header.php' ?>
</head>
<body>
<div class="container mt-1">
    <div class="table-responsive mt-4">
        <h4><i class="fas fa-users"></i> ข้อมูลผู้ใช้ทั้งหมด</h4>
        <table id="usersTable" class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th class="text-white">No.</th>
                    <th class="text-white">User ID</th>
                    <th class="text-white">ชื่อ</th>
                    <th class="text-white">นามสกุล</th>
                    <th class="text-white">อีเมล</th>
                    <th class="text-white">หมายเลขโทรศัพท์</th>
                    <th class="text-white">วันสร้าง</th>
                    <th class="text-white">วันอัปเดต</th>
                    <th class="text-white">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                try {
                    require_once(__DIR__ . '/../../../lib/connect.php');
                    $stmt = $conn->prepare("
                        SELECT user_id, first_name, last_name, email, phone_number, date_create, date_update
                        FROM mb_user 
                        ORDER BY date_create DESC
                    ");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $counter = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $counter++ . '</td>';
                            echo '<td>' . htmlspecialchars($row['user_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['phone_number']) . '</td>';
                            echo '<td>' . date('d/m/Y H:i', strtotime($row['date_create'])) . '</td>';
                            echo '<td>' . ($row['date_update'] ? date('d/m/Y H:i', strtotime($row['date_update'])) : 'N/A') . '</td>';
                            echo '<td>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="' . htmlspecialchars($row['user_id']) . '"><i class="fas fa-trash-alt"></i> ลบ</button>
                                  </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="9" class="text-center">ไม่มีข้อมูลผู้ใช้</td></tr>';
                    }
                    $stmt->close();
                    $conn->close();
                } catch (Exception $e) {
                    echo '<tr><td colspan="9" class="text-danger">เกิดข้อผิดพลาด: ' . $e->getMessage() . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script src='js/edit_users.js?v=<?php echo time(); ?>'></script>
<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
</body>
</html>