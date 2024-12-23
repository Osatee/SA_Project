<?php
session_start();

if(!isset($_SESSION['fname']) || !isset($_SESSION['lname'])) {
    header("location: login.php");
    exit();
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

</head>
<style>
.user-card {
    background-color: white;
    border-radius: 8px;
    padding: 200px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 1400px;
    margin: 150px auto;
}

.user-info {
    display: flex;
    align-items: flex-start;
}

.user-photo {
    width: 300px;
    height: 300px;
    background-color: #ddd;
    margin-right: 100px;
}

.user-details {
    flex-grow: 1;
}

.user-details p {
    margin: 15px 0;
    font-size: 25px;
    display: flex;
    align-items: baseline;
}

.label {
    font-weight: bold;
    width: 200px;
    display: inline-block;
    flex-shrink: 0;
}

.value {
    flex-grow: 1;
}

.value.email {
    color: #007bff;
}
</style>
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
			<li class="active">
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
        <div class="user-card">
			<div class="user-info">
				<div class="user-photo"></div>
				<div class="user-details">
					<p><span class="label">ชื่อ-นามสกุล :</span><?php echo $_SESSION['fname'] . ' ' . $_SESSION['lname']; ?></p>
					<p><span class="label">แผนก :</span> <span class="value"><?php echo $_SESSION['dept_name']; ?></span></p>
					<p><span class="label">ตำแหน่ง :</span> <span class="value"><?php echo $_SESSION['pos_name']; ?></span></p>
					<p><span class="label">อีเมล :</span> <span class="value email"><?php echo $_SESSION['email']; ?></span></p>
				</div>
			</div>
    	</div>
	</section>
	<!-- CONTENT -->
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
	

	<script src="script.js"></script>
</body>
</html>