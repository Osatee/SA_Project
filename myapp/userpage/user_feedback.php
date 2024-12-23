<?php
// เริ่ม session
session_start();

include "../config.php"; // ใช้ฟังก์ชัน getDBConnection จากไฟล์ config.php

// สร้างการเชื่อมต่อฐานข้อมูล
$conn = getDBConnection(); // ตรวจสอบว่ามีการสร้างการเชื่อมต่อ

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

// ตรวจสอบว่ามีการ login หรือไม่
if(isset($_SESSION['p_id'])) {
    $p_id = $_SESSION['p_id'];
    
    // Query ดึงข้อมูล feedback โดย p_id ตรงกับของ session
    $sql = "SELECT detail, dated FROM feedback WHERE p_id = :p_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':p_id', $p_id, PDO::PARAM_INT);
    $stmt->execute();
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	
	<!-- My CSS -->
	<link rel="stylesheet" href="style_user.css">

	<title>Yakuza</title>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"></script>

	<style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
        }
		h2 {
            color: #333;
			font-size: 60px;
        }
        p {
            font-size: 20px;
            margin: 10px 0;
        }
        .status {
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
        }
        .done {
            color: white;
            background-color: #28a745; /* สีเขียวสำหรับสถานะ "ทำแล้ว" */
        }
        .not-done {
            color: white;
            background-color: #dc3545; /* สีแดงสำหรับสถานะ "ยังไม่มี" */
        }
    </style>

	<style>
        .container {
			margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
        }

        .container .table h1 {
            font-size: 36px; /* ขนาดตัวอักษรใหญ่ */
            color: #ff6347; /* สีข้อความ (เช่น สีส้มแดง) */
            text-align: center; /* จัดข้อความให้อยู่ตรงกลาง */
            background-color: #f9f9f9; /* พื้นหลังสีเทาอ่อน */
            padding: 20px; /* เพิ่มช่องว่างภายในเพื่อความสวยงาม */
            border-radius: 10px; /* ทำมุมโค้ง */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* เพิ่มเงาเพื่อให้เด่นขึ้น */
            margin-bottom: 20px; /* เพิ่มช่องว่างระหว่าง h1 กับตาราง */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #f2f2f2;
        }

    </style>

</head>
<body>


	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="index.php" class="brand">
			<i class='bx bxs-report'></i>
			<span class="text">USERS</span>
		</a>
		<ul class="side-menu top">
		<li>
				<a href="index.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">แดชบอร์ด</span>
				</a>
			</li>
			<li>
				<a href="user_info.php">
                    <i class='bx bxs-user-detail'></i>
					<span class="text">ข้อมูลส่วนตัว</span>
				</a>
			</li>
			<li>
				<a href="form_1.php">
                    <i class='bx bxs-notepad'></i>
					<span class="text">แบบประเมินการทำงาน</span>
				</a>
			</li>
            <li>
				<a href="form_2.php">
					<i class='bx bxs-notepad'></i>
					<span class="text">แบบประเมินความประพฤติ</span>
				</a>
			</li>
            <li class="active">
				<a href="user_feedback.php">
					<i class='bx bxs-comment-detail'></i>
					<span class="text">ข้อเสนอแนะของฉัน</span>
				</a>
			</li>
		<ul class="side-menu">
			<li>
				<a href="#" class="logout" onclick="confirmLogout(event)">
					<i class='bx bxs-log-out-circle' ></i>
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
        <div class="container">
            <h1 style="font-size: 36px; color: #ff6347;
            text-align: center;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;">Your Feedback</h1>
            
            <?php if (!empty($feedbacks)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Detail</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['detail']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['dated']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No feedback available.</p>
            <?php endif; ?>
        </div>

        </main>
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
	<script>
		document.getElementById("downloadPDF").addEventListener("click", function() {
			// เลือกเฉพาะส่วนที่เป็นกราฟ
			var canvasElement = document.getElementById('evaluationChart');
	
			// ใช้ html2canvas เพื่อจับภาพส่วนที่ต้องการ
			html2canvas(canvasElement, {
				onrendered: function(canvas) {
					var imgData = canvas.toDataURL('image/png'); // แปลง canvas เป็นภาพ
					var pdf = new jsPDF('landscape'); // สร้างเอกสาร PDF ขนาดแนวนอน
	
					// เพิ่มภาพลงใน PDF
					pdf.addImage(imgData, 'PNG', 10, 10, canvas.width / 8, canvas.height / 8);
	
					// ดาวน์โหลด PDF
					pdf.save("chart.pdf");
				}
			});
		});
	</script>
	
	</section>
	<!-- CONTENT -->
	

	<script src="script.js"></script>
</body>
</html>