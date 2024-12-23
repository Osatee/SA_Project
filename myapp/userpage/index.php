<?php
	session_start();
	// เชื่อมต่อฐานข้อมูล
	include "../config.php"; // ใช้ฟังก์ชัน getDBConnection จากไฟล์ config.php

	// สร้างการเชื่อมต่อฐานข้อมูล
	$conn = getDBConnection(); // ตรวจสอบว่ามีการสร้างการเชื่อมต่อ

	// ตรวจสอบการเชื่อมต่อ
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}

	// ดึง p_id จาก session
	if (!isset($_SESSION['p_id'])) {
		header("Location: ../login.php");
		exit();
	}

	$lname = $_SESSION['lname']; // เก็บค่าที่ query ได้ไว้ในตัวแปร
	$fname = $_SESSION['fname']; // เก็บค่าที่ query ได้ไว้ในตัวแปร
	$p_id = $_SESSION['p_id'];

	// Query ข้อมูลสถานะการทำแบบประเมินของพนักงานที่ล็อกอินอยู่
	$sql = "SELECT p.p_id, p.fname, 
				CASE 
					WHEN f.evaluator IS NOT NULL THEN 'ทำแล้ว' 
					ELSE 'ยังไม่ทำ' 
				END AS evaluation_status 
			FROM person p 
			LEFT JOIN form f ON p.p_id = f.evaluator 
			WHERE p.p_id = :p_id";
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':p_id', $p_id, PDO::PARAM_INT);
	$stmt->execute();
	$evaluationResult = $stmt->fetch(PDO::FETCH_ASSOC);



		$sql1 = "SELECT 
				AVG(score1) AS avg_score1,
				AVG(score2) AS avg_score2,
				AVG(score3) AS avg_score3,
				AVG(score4) AS avg_score4,
				AVG(score5) AS avg_score5,
				AVG(score6) AS avg_score6,
				AVG(score7) AS avg_score7
			FROM form
			WHERE evaluatee = :p_id AND topic_id = 1";
	$stmt1 = $conn->prepare($sql1);
	$stmt1->bindParam(':p_id', $p_id, PDO::PARAM_INT);
	$stmt1->execute();
	$averageScores1 = $stmt1->fetch(PDO::FETCH_ASSOC);

	// Query ค่าเฉลี่ยของคะแนนจาก topic_id = 2
	$sql2 = "SELECT 
				AVG(score1) AS avg_score1,
				AVG(score2) AS avg_score2,
				AVG(score3) AS avg_score3,
				AVG(score4) AS avg_score4,
				AVG(score5) AS avg_score5,
				AVG(score6) AS avg_score6,
				AVG(score7) AS avg_score7
			FROM form
			WHERE evaluatee = :p_id AND topic_id = 2";
	$stmt2 = $conn->prepare($sql2);
	$stmt2->bindParam(':p_id', $p_id, PDO::PARAM_INT);
	$stmt2->execute();
	$averageScores2 = $stmt2->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT sal AS salary FROM person"; // นับจำนวนรายการในตาราง person
	$stmt = $conn->query($sql);
	$stmt->execute();
	$data = $stmt->fetch(PDO::FETCH_ASSOC);
	$salary = $data['salary']; // เก็บค่าที่ query ได้ไว้ในตัวแปร

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
        h1 {
            color: #333;
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
		<li class="active">
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
            <li>
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
				<h1>Evaluation Status</h1>

				<?php
				// แสดงข้อความสถานะการทำแบบประเมิน
				if ($evaluationResult) {
					echo "<h2>สวัสดีคุณ " . $evaluationResult['fname'] . "</h2>";

					// ตรวจสอบสถานะและเพิ่มคลาสที่เหมาะสม
					if ($evaluationResult['evaluation_status'] == 'ทำแล้ว') {
						echo "<p class='status done'>สถานะการทำแบบประเมินของคุณ: ทำแล้ว</p>";
					} else {
						echo "<p class='status not-done'>สถานะการทำแบบประเมินของคุณ: ยังไม่ทำ</p>";
					}
				} else {
					echo "<p>ไม่พบข้อมูลการประเมิน</p>";
				}
				?>

				<ul class="box-info">
								<li>
									<i class='bx bxs-card'></i>
									<span class="text">
										<p>Firstname</p>
										<h3><?php echo$fname ?></h3>
									</span>
								</li>
								<li>
									<i class='bx bxs-group' ></i>
									<span class="text">
										<p>Lastname</p>
										<h3><?php echo$lname ?></h3>
									</span>
								</li>
								<li>
									<i class='bx bxs-dollar-circle' ></i>
									<span class="text">
										<p>Salary</p>
										<h3><?php echo$salary ?></h3>
									</span>
								</li>
				</ul>
			</div>

		</main>
		<!-- MAIN -->

		<div class="container">
    <h1>Average Evaluation Scores for Topic 1</h1>
    <canvas id="evaluationChart1"></canvas>
</div>

<div class="container">
    <h1>Average Evaluation Scores for Topic 2</h1>
    <canvas id="evaluationChart2"></canvas>
</div>

<script>
// กราฟสำหรับ topic_id 1
var ctx1 = document.getElementById('evaluationChart1').getContext('2d');
var evaluationChart1 = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: ['ประสิทธิภาพในการทำงาน', 'การทำงานงานร่วมกับผู้อื่น', 'การสื่อสารและประสานงาน', 'การพัฒนาทางด้านการทำงาน', 'การจัดการเวลา', 'การปรับตัว', 'การแก้ปัญหา'],
        datasets: [{
            label: 'Average Scores (Topic 1)',
            data: [
                <?php echo $averageScores1['avg_score1']; ?>, 
                <?php echo $averageScores1['avg_score2']; ?>, 
                <?php echo $averageScores1['avg_score3']; ?>, 
                <?php echo $averageScores1['avg_score4']; ?>, 
                <?php echo $averageScores1['avg_score5']; ?>, 
                <?php echo $averageScores1['avg_score6']; ?>, 
                <?php echo $averageScores1['avg_score7']; ?>
            ],
            backgroundColor: 'rgba(54, 162, 235, 1)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                max: 10
            }
        }
    }
});

// กราฟสำหรับ topic_id 2
var ctx2 = document.getElementById('evaluationChart2').getContext('2d');
var evaluationChart2 = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ['ความใจดี', 'ความเป็นผู้นำ', 'ความเป็นที่รักของพนักงานอื่น', 'ความขยัน', 'ความมีน้ำใจ', 'การเรียนรู้สิ่งใหม่', 'การทำงาน'],
        datasets: [{
            label: 'Average Scores (Topic 2)',
            data: [
                <?php echo $averageScores2['avg_score1']; ?>, 
                <?php echo $averageScores2['avg_score2']; ?>, 
                <?php echo $averageScores2['avg_score3']; ?>, 
                <?php echo $averageScores2['avg_score4']; ?>, 
                <?php echo $averageScores2['avg_score5']; ?>, 
                <?php echo $averageScores2['avg_score6']; ?>, 
                <?php echo $averageScores2['avg_score7']; ?>
            ],
            backgroundColor: 'rgba(255, 99, 132, 1)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                max: 10
            }
        }
    }
});
</script>
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