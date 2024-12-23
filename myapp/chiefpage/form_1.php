<?php
session_start();

if (!isset($_SESSION['fname']) || !isset($_SESSION['lname'])) {
    header("location: login.php");
    exit();
}
?>

<?php
// เชื่อมต่อฐานข้อมูล
include "../config.php"; // ใช้ฟังก์ชัน getDBConnection จากไฟล์ config.php

$evaluator_id = $_SESSION['p_id']; // เก็บ p_id ของผู้ที่ล็อกอินอยู่

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบว่ามีข้อมูลถูกส่งมาครบถ้วน
    if (
        isset($_POST['employee'], $_POST['question1'], $_POST['question2'], $_POST['question3'], $_POST['question4'], $_POST['question5'], $_POST['question6'], $_POST['question7'])
    ) {
        $evaluatee = $_POST['employee']; // ผู้ถูกประเมิน
        $topic_id = 1; // topic_id 1 สำหรับแบบประเมินด้านการทำงาน

        // รับค่าคะแนนการประเมินจากแต่ละคำถาม
        $question1 = $_POST['question1'];
        $question2 = $_POST['question2'];
        $question3 = $_POST['question3'];
        $question4 = $_POST['question4'];
        $question5 = $_POST['question5'];
        $question6 = $_POST['question6'];
        $question7 = $_POST['question7'];

        // สร้างการเชื่อมต่อฐานข้อมูล
        $conn = getDBConnection();

        if ($conn) {
            try {
                // เตรียมคำสั่ง SQL เพื่อบันทึกข้อมูลการประเมิน
                $sql = "INSERT INTO form (topic_id, evaluator, evaluatee, score1, score2, score3, score4, score5, score6, score7)
                        VALUES (:topic_id, :evaluator, :evaluatee, :score1, :score2, :score3, :score4, :score5, :score6, :score7)";

                // เตรียม statement
                $stmt = $conn->prepare($sql);

                // ผูกค่ากับคำสั่ง SQL
                $stmt->bindParam(':topic_id', $topic_id);
                $stmt->bindParam(':evaluator', $evaluator_id);
                $stmt->bindParam(':evaluatee', $evaluatee);
                $stmt->bindParam(':score1', $question1);
                $stmt->bindParam(':score2', $question2);
                $stmt->bindParam(':score3', $question3);
                $stmt->bindParam(':score4', $question4);
                $stmt->bindParam(':score5', $question5);
                $stmt->bindParam(':score6', $question6);
                $stmt->bindParam(':score7', $question7);

                // Execute และตรวจสอบผลลัพธ์
                if ($stmt->execute()) {
                    echo "<script>alert('การประเมินสำเร็จ!'); window.location.href='feedback.php';</script>";
                } else {
                    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก!');</script>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "<script>alert('ไม่สามารถเชื่อมต่อฐานข้อมูลได้');</script>";
        }
    } else {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน!');</script>";
    }
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
	<link rel="stylesheet" href="style_chief.css">

	<title>Yakuza</title>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"></script>
<style>

    .container-fluid {
        margin: 30px;
    }

    .card {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 10px;
    }

    .card-title {
        font-size: 3rem;
        color: #212529;
    }

    .form-label {
        margin: 5px;
        font-size: 1.5rem;
        font-weight: bold;
    }

    p {
        margin: 5px;
        font-size: 1.25rem;
        color: #6c757d;
    }

    .btn-success {
            margin-top: 25px;
            background-color: #00c292;
            border-color: #00c292;
            font-size: 1.1rem;
            padding: 12px 24px;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: #00a579;
            border-color: #00a579;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

    .d-flex {
        justify-content: center;
        align-items: center;
    }

    .form-check-inline {
        margin-left: 10px;
        margin-right: 10px;
    }

    .form-check-label {
        margin-left: 5px;
        font-size: 1.2rem;
    }

    #employee {
            font-size: 1.2rem;
            padding: 10px;
            width: 50%;
            margin-bottom: 20px;
    }

    .form-group label[for="employee"] {
            font-size: 1.8rem;
            margin-bottom: 10px;
            display: block;
    }

</style>
</head>
<body>
	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="index.php" class="brand">
			<i class='bx bxs-report'></i>
			<span class="text">CHIEF</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="index.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="user_info.php">
                    <i class='bx bxs-user-detail'></i>
					<span class="text">User_Info</span>
				</a>
			</li>
			<li>
				<a href="form_1.php">
                    <i class='bx bxs-notepad'></i>
					<span class="text">Form_1</span>
				</a>
			</li>
            <li>
				<a href="form_2.php">
					<i class='bx bxs-notepad'></i>
					<span class="text">Form_2</span>
				</a>
			</li>
            <li>
				<a href="feedback.php">
                    <i class='bx bxs-comment-detail'></i>
					<span class="text">Feedback</span>
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

        <div class="container-fluid">
            <div class="container-fluid">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="employee" style="color:red; font-weight: bold;">เลือกผู้ประเมิน</label>
                        <select name="employee" id="employee" required>
                            <option value="">--เลือก ผู้ถูกประเมิน--</option>
                            <?php
                            $conn = getDBConnection();
                            if ($conn) {
                                $sql = "SELECT p_id, fname FROM person";
                                $stmt = $conn->query($sql);
                                if ($stmt->rowCount() > 0) {
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $row["p_id"] . '">' . $row["fname"] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">ไม่มีผู้ถูกประเมิน</option>';
                                }
                            } else {
                                echo '<option value="">ไม่สามารถเชื่อมต่อฐานข้อมูลได้</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">แบบประเมินในด้านการทำงาน</h5>

                            <!-- Questions -->
                            <?php
                            $questions = [
                                "ประสิทธิภาพในการทำงาน",
                                "การทำงานงานร่วมกับผู้อื่น",
                                "การสื่อสารและประสานงาน",
                                "การพัฒนาทางด้านการทำงาน",
                                "การจัดการเวลา",
                                "การปรับตัว",
                                "การแก้ปัญหา"
                            ];
                            for ($q = 1; $q <= 7; $q++) { ?>
                                <div class="mb-3">
                                    <label class="form-label"><?= $questions[$q - 1] ?></label>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="question<?= $q ?>" value="<?= $i ?>" required>
                                                <label class="form-check-label"><?= $i ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="mb-3 mt-4">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </form>

    </section>
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
