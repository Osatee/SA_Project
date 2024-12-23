<?php
session_start();
include "../config.php";

$conn = getDBConnection();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query ข้อมูลจากฐานข้อมูล
$sql = "SELECT COUNT(*) AS persons FROM person"; // นับจำนวนรายการในตาราง person
$stmt = $conn->query($sql);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
$persons = $data['persons']; // เก็บค่าที่ query ได้ไว้ในตัวแปร

// Query ข้อมูลจากฐานข้อมูล
$sql = "SELECT COUNT(*) AS total_evaluators FROM (
													SELECT DISTINCT evaluator
													   FROM form
													) AS subquery;";
$stmt = $conn->query($sql);
$stmt->execute();
$totalEvaluators = $stmt->fetch(PDO::FETCH_ASSOC)['total_evaluators'];

$sql = "SELECT COUNT(p.p_id) AS count_not_in_evaluator FROM person p LEFT JOIN form f ON p.p_id = f.evaluator WHERE f.evaluator IS NULL;";
$stmt = $conn->query($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC)['count_not_in_evaluator'];

$sql = "SELECT COUNT(*) AS forms FROM form"; // นับจำนวนรายการในตาราง person
$stmt = $conn->query($sql);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
$forms = $data['forms']; 

// ฟังก์ชันสำหรับดึงข้อมูลทั้งหมด
// ฟังก์ชันสำหรับดึงข้อมูลตาม query ที่เลือก
function getQueryData($conn, $queryType) {
    switch ($queryType) {
        case 'all_employees':
            $sql = "SELECT p.fname as ชื่อจริง, p.lname as นามสกุล, pos.pos_name as ตำแหน่ง, d.dept_name as แผนก
                    FROM person p 
                    JOIN position pos ON p.pos_id = pos.pos_id
                    JOIN department d ON p.dept_id = d.dept_id
                    ORDER BY 
                        CASE 
                            WHEN pos.pos_name = 'Chief' THEN 1
                            WHEN pos.pos_name = 'HR' THEN 2
                            WHEN pos.pos_name = 'User' THEN 3
                            ELSE 4
                        END, 
                        p.fname ASC"; // Additional ordering by first name if positions are the same
            break;
        case 'completed_evaluations':
            $sql = "SELECT DISTINCT p.fname as ชื่อจริง, p.lname as นามสกุล, pos.pos_name as ตำแหน่ง, d.dept_name as แผนก
                    FROM person p
                    JOIN position pos ON p.pos_id = pos.pos_id
                    JOIN department d ON p.dept_id = d.dept_id
                    INNER JOIN form f ON p.p_id = f.evaluator
                    ORDER BY 
                        CASE 
                            WHEN pos.pos_name = 'Chief' THEN 1
                            WHEN pos.pos_name = 'HR' THEN 2
                            WHEN pos.pos_name = 'User' THEN 3
                            ELSE 4
                        END, 
                        p.fname ASC";
            break;
        case 'pending_evaluations':
            $sql = "SELECT p.fname as ชื่อจริง, p.lname as นามสกุล, pos.pos_name as ตำแหน่ง, d.dept_name as แผนก 
                    FROM person p
                    JOIN position pos ON p.pos_id = pos.pos_id
                    JOIN department d ON p.dept_id = d.dept_id
                    LEFT JOIN form f ON p.p_id = f.evaluator 
                    WHERE f.evaluator IS NULL
                    ORDER BY 
                        CASE 
                            WHEN pos.pos_name = 'Chief' THEN 1
                            WHEN pos.pos_name = 'HR' THEN 2
                            WHEN pos.pos_name = 'User' THEN 3
                            ELSE 4
                        END, 
                        p.fname ASC";
            break;
        case 'all_forms':
            $sql = "SELECT p1.fname as ผู้ประเมิน, p2.fname as ผู้ถูกประเมิน, t.topic_name as หัวข้อการประเมิน, 
                           f.score1 as 'คะแนนหัวข้อที่ 1', f.score2 as 'คะแนนหัวข้อที่ 2', f.score3 as 'คะแนนหัวข้อที่ 3',
                           f.score4 as 'คะแนนหัวข้อที่ 4', f.score5 as 'คะแนนหัวข้อที่ 5', f.score6 as 'คะแนนหัวข้อที่ 6', 
                           f.score7 as 'คะแนนหัวข้อที่ 7' 
                    FROM form f 
                    JOIN topic t ON f.topic_id = t.topic_id
                    JOIN person p1 ON f.evaluator = p1.p_id
                    JOIN person p2 ON f.evaluatee = p2.p_id";
            break;
        default:
            $sql = "SELECT * FROM person";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ตรวจสอบว่ามีการเลือก option หรือไม่
$selectedOption = isset($_GET['option']) ? $_GET['option'] : 'all_employees';

// ดึงข้อมูลตาม query ที่เลือก
$queryData = getQueryData($conn, $selectedOption);

// ฟังก์ชันสำหรับสร้าง table headers จาก keys ของข้อมูลแถวแรก
function getTableHeaders($data) {
    if (empty($data)) return [];
    return array_keys($data[0]);
}

$tableHeaders = getTableHeaders($queryData);

// Get the current script name without the directory path or file extension
$current_page = basename($_SERVER['SCRIPT_NAME'], ".php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	
	<!-- My CSS -->
	<link rel="stylesheet" href="style_admin.css">

	<title>Yakuza</title>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"></script>

	<style>
        .dashboard-table {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            margin-top: 30px;
            overflow-x: auto;
        }
        .dashboard-table select {
            float: right;
            margin-bottom: 20px;
            padding: 12px 20px;
            font-size: 16px;
            border: 2px solid #3498db;
            border-radius: 8px;
            background-color: #fff;
            color: #3498db;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .dashboard-table select:hover {
            background-color: #3498db;
            color: #fff;
        }
        .dashboard-table table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .dashboard-table th, .dashboard-table td {
            padding: 15px;
            border: 1px solid #e0e0e0;
            text-align: left;
            font-size: 16px;
        }
        .dashboard-table th {
            background-color: #3498db;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
        }
        .dashboard-table tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        .dashboard-table tr:hover {
            background-color: #e8f4f8;
        }
        .dashboard-table td {
            transition: all 0.3s ease;
        }
        .dashboard-table td:hover {
            background-color: #d4e6f1;
        }
    </style>
</head>
<body>


<!-- SIDEBAR -->
<section id="sidebar">
    <a href="index.php" class="brand">
        <i class='bx bxs-report'></i>
        <span class="text">HR</span>
    </a>
    <ul class="side-menu top">
        <li class="<?php echo ($current_page === 'index') ? 'active' : ''; ?>">
            <a href="index.php">
                <i class='bx bxs-dashboard'></i>
                <span class="text">แดชบอร์ด</span>
            </a>
        </li>
        <li class="<?php echo ($current_page === 'user_info') ? 'active' : ''; ?>">
            <a href="user_info.php">
                <i class='bx bxs-user-detail'></i>
                <span class="text">ข้อมูลส่วนตัว</span>
            </a>
        </li>
        <li class="<?php echo ($current_page === 'form_1') ? 'active' : ''; ?>">
            <a href="form_1.php">
                <i class='bx bxs-user-detail'></i>
                <span class="text">แบบประเมินการทำงาน</span>
            </a>
        </li>
        <li class="<?php echo ($current_page === 'form_2') ? 'active' : ''; ?>">
            <a href="form_2.php">
                <i class='bx bxs-user-detail'></i>
                <span class="text">แบบประเมินความประพฤติ</span>
            </a>
        </li>
        <li class="<?php echo ($current_page === 'result') ? 'active' : ''; ?>">
            <a href="result.php">
                <i class='bx bxs-user-detail'></i>
                <span class="text">สรุปการประเมิน</span>
            </a>
        </li>
    </ul>
    <ul class="side-menu">
        <li>
            <a href="#" class="logout" onclick="confirmLogout(event)">
                <i class='bx bxs-log-out-circle'></i>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>
<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu' ></i>
			<form action="#">
				<div class="form-input">
					<input type="search" placeholder="Search...">
					<button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
				</div>
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
			<a href="#" class="profile">
				<img src="https://st4.depositphotos.com/9998432/24428/v/450/depositphotos_244284796-stock-illustration-person-gray-photo-placeholder-man.jpg">
			</a>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>

			<ul class="box-info">
				<li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<p>พนักงานทั้งหมด</p>
						<h3><?php echo$persons ?></h3>
					</span>
				</li>

				<li>
					<i class='bx bxs-user-check'></i>
					<span class="text">
						<p>ทำแบบประเมินแล้ว</p>
						<h3><?php echo$totalEvaluators ?></h3>
					</span>
				</li>

				<li>
					<i class='bx bxs-user-x' ></i>
					<span class="text">
						<p>ยังไม่ทำแบบประเมิน</p>
						<h3><?php echo$result ?></h3>
					</span>
				</li>

				<li>
					<i class='bx bxs-notepad'></i>
					<span class="text">
						<p>แบบฟอร์มทั้งหมด</p>
						<h3><?php echo$forms ?></h3>
					</span>
				</li>

				</ul>

            </ul>

            <!-- เพิ่มส่วนของตารางใหม่ -->
            <div class="dashboard-table">
                <form action="" method="get">
                    <select name="option" onchange="this.form.submit()">
                        <option value="all_employees" <?php echo $selectedOption == 'all_employees' ? 'selected' : ''; ?>>พนักงานทั้งหมด</option>
                        <option value="completed_evaluations" <?php echo $selectedOption == 'completed_evaluations' ? 'selected' : ''; ?>>ทำแบบประเมินแล้ว</option>
                        <option value="pending_evaluations" <?php echo $selectedOption == 'pending_evaluations' ? 'selected' : ''; ?>>ยังไม่ทำแบบประเมิน</option>
                        <option value="all_forms" <?php echo $selectedOption == 'all_forms' ? 'selected' : ''; ?>>แบบฟอร์มทั้งหมด</option>
                    </select>
                </form>
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($tableHeaders as $header): ?>
                                <th><?php echo htmlspecialchars($header); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queryData as $row): ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

		</main>

		<!-- Sections to display tables -->

	<script>
		function confirmLogout(event) {
			event.preventDefault(); // ป้องกันไม่ให้กดแล้วออกทันที
	
			// แสดงกล่องข้อความยืนยัน
			var userConfirmation = confirm("Are you sure you want to log out?");
			
			// ถ้าผู้ใช้กด OK
			if (userConfirmation) {
				window.location.href = "../login.php"; // นำผู้ใช้ไปยัง logout.php
			}
		}
	</script>

	</section>
	<!-- CONTENT -->
	

	<script src="script.js"></script>
</body>
</html>