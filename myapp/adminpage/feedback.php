<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";  // ชื่อเซิร์ฟเวอร์ฐานข้อมูล
$username = "root";         // ชื่อผู้ใช้ฐานข้อมูล
$password = "";             // รหัสผ่านของฐานข้อมูล
$dbname = "sa";  // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลแผนกจากตาราง department
$sql_departments = "SELECT dept_id, dept_name FROM department";
$result_departments = $conn->query($sql_departments);

// ดึงข้อมูลพนักงานจากตาราง person
$sql_employees = "SELECT p_id, fname, dept_id FROM person";
$result_employees = $conn->query($sql_employees);
 
// แปลงข้อมูลพนักงานเป็น JSON เพื่อใช้ใน JavaScript
$employees = [];
if ($result_employees->num_rows > 0) {
    while($row = $result_employees->fetch_assoc()) {
        $employees[] = $row;
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
	<link rel="stylesheet" href="style_admin.css">

	<title>Yakuza</title>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"></script>
<style>
    .container-fluid {
        margin: 30px;
    }

    .card {
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
    }

    .btn-success:hover {
        background-color: #009e73;
        border-color: #009e73;
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

</style>

<style>
        .container {
			background-color: white;
            max-width: 1500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }
		
        label {
            display: block;
            margin-bottom: 5px;
            font-size: 1.2rem;
            font-weight: bold;
        }
		
        select, input, textarea {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
		
        button {
            display: block;
            width: 150px;
            background-color: #00c292;
            color: white;
            padding: 10px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 20px auto 0 auto; /* Center button horizontally */
        }
        button:hover {
            background-color: #009e73;
        }
</style>

</head>
<body>
	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bxs-report'></i>
			<span class="text">YAKUZA SU</span>
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
				<a href="#">
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

    </section>

	<div class="container">
        <form method="POST">
            <!-- Department Field -->
            <div class="form-group">
                <label for="department">แผนก</label>
                <select name="department" id="department" required onchange="filterEmployees()">
                <option value="">--Select Department--</option>
                <?php
                // แสดงตัวเลือกแผนก
                if ($result_departments->num_rows > 0) {
                    while($row = $result_departments->fetch_assoc()) {
                        echo '<option value="' . $row["dept_id"] . '">' . $row["dept_name"] . '</option>';
                    }
                }
                ?>
            </select>
            </div>
            <!-- Employee Field -->
            <div class="form-group">
                <label for="employee">ชื่อพนักงาน</label>
                <select name="employee" id="employee" required>
                    <option value="">--Select Employee--</option>
                </select>
                <?php
						// เพิ่มตัวเลือกพนักงาน
						if (!empty($filteredEmployees)) {
							foreach ($filteredEmployees as $employee) {
								echo '<option value="' . $employee['p_id'] . '">' . $employee['fname'] . '</option>';
							}
						}
					?>
            </div>
            <!-- Details Field -->
            <div class="form-group">
                <label for="details">ข้อความ</label>
                <textarea name="details" id="details" rows="5" placeholder="Enter details" required></textarea>
            </div>
            <!-- Date Field -->
            <div class="form-group">
                <label for="dated">วันที่ส่ง</label>
                <input type="date" name="dated" id="dated" required>
            </div>
            <!-- Submit Button -->
            <button type="submit">Submit</button>
            <?php
				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					// รับข้อมูลจากฟอร์ม
					$department = $_POST['department'];
					$employee = $_POST['employee'];
					$details = $_POST['details'];
					$feedbackDate = $_POST['dated'];

					// เตรียมคำสั่ง SQL สำหรับบันทึกข้อมูล (ใช้ ? เป็น placeholder)
					$sql = "INSERT INTO feedback (dept_id, p_id, detail, dated) VALUES (?, ?, ?, ?)";

					// เตรียม execute statement
					$stmt = $conn->prepare($sql);
					// bind ค่าให้กับ placeholder
					$stmt->bind_param("iiss", $department, $employee, $details, $feedbackDate);

					// execute คำสั่ง SQL
					if ($stmt->execute()) {
						// หากสำเร็จจะแสดงข้อความแจ้งเตือน
						echo "<script>alert('Feedback submitted successfully!'); window.location.href='feedback.php';</script>";
					} else {
						// กรณีเกิดข้อผิดพลาด
						echo "Error: " . $stmt->error;
					}

					// ปิด statement
					$stmt->close();
				}
			?>
        </form>
    </div>
	<script>
		// เก็บข้อมูลพนักงานในรูปแบบ JSON (จาก PHP)
		var employees = <?php echo json_encode($employees); ?>;

		// ฟังก์ชั่นกรองพนักงานตามแผนก
		function filterEmployees() {
			var departmentSelect = document.getElementById("department");
			var employeeSelect = document.getElementById("employee");
			var selectedDeptId = departmentSelect.value;

			// ล้างตัวเลือกพนักงานก่อนหน้า
			employeeSelect.innerHTML = '<option value="">--Select Employee--</option>';

			// กรองพนักงานที่มี dept_id ตรงกับแผนกที่เลือก
			var filteredEmployees = employees.filter(function(employee) {
				return employee.dept_id == selectedDeptId;
			});

			// เพิ่มพนักงานที่กรองแล้วเข้าไปใน dropdown
			filteredEmployees.forEach(function(employee) {
				var option = document.createElement("option");
				option.value = employee.p_id;
				option.text = employee.fname;
				employeeSelect.appendChild(option);
			});
		}
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
