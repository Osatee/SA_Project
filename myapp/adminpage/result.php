<?php
session_start(); // เปิดการใช้งาน session

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['p_id'])) {
    // ถ้ายังไม่ได้เข้าสู่ระบบ ให้ไปที่หน้า login
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "sa");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all employees for the dropdown
$employeeQuery = "SELECT p_id, fname, lname FROM person";
$employeeResult = $conn->query($employeeQuery);

// Initialize variables for evaluation
$totalScore = 0;
$maxScore = 0;
$evaluationResult = "";
$personResult = [];
$scoresData = [];
$topics = [];

// Check if an employee is selected
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee'])) {
    $p_id = $_POST['employee'];
    $_SESSION['selected_employee'] = $p_id; // Save selected employee to session

    // Query to get person information
    $personQuery = "SELECT fname, lname FROM person WHERE p_id = ?";
    $stmt = $conn->prepare($personQuery);
    $stmt->bind_param("i", $p_id);
    $stmt->execute();
    $personResult = $stmt->get_result()->fetch_assoc();

    // Query to get scores and topics
    $formQuery = "
        SELECT f.topic_id, t.topic_name, f.score1, f.score2, f.score3, f.score4, f.score5, f.score6, f.score7
        FROM form f
        INNER JOIN topic t ON f.topic_id = t.topic_id
        WHERE f.evaluatee = ?
    ";
    $stmt = $conn->prepare($formQuery);
    $stmt->bind_param("i", $p_id);
    $stmt->execute();
    $formResults = $stmt->get_result();

    // Calculate total score and percentage
    while ($row = $formResults->fetch_assoc()) {
        $sumScores = $row['score1'] + $row['score2'] + $row['score3'] + $row['score4'] + $row['score5'] + $row['score6'] + $row['score7'];
        $totalScore += $sumScores;
        $maxScore += 7 * 5; // Assuming max score for each question is 5

         // Prepare data for graph
         $topics[] = $row['topic_name'];
         $scoresData[] = $sumScores;
    }

    // Calculate percentage
    if ($maxScore > 0) {
        $percentage = ($totalScore / $maxScore) * 100;
    } else {
        $percentage = 0;
    }

    // Determine the evaluation result based on percentage
    if ($percentage >= 75) {
        $evaluationResult = "ดีมาก";
    } elseif ($percentage >= 50) {
        $evaluationResult = "ดี";
    } elseif ($percentage >= 30){
        $evaluationResult = "พอใช้";
    } else {
        $evaluationResult = "ปรับปรุง";
    }
}

// Get selected employee from session if available
$selectedEmployee = isset($_SESSION['selected_employee']) ? $_SESSION['selected_employee'] : '';

// Close connection
$conn->close();
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

</head>
<style>
.user-card {
    background-color: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 1400px;
    margin: 90px auto;
    text-align: center;
}

#scoreChart {
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f8f9fa;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.user-card form {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-bottom: 30px;
}

.user-card select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    width: 250px;
}

.user-card button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

.user-card button:hover {
    background-color: #0056b3;
}

.user-info {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    text-align: left;
    margin-top: 20px;
}

.user-details {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.user-details h2 {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.user-details p {
    font-size: 18px;
    color: #555;
}

.label {
    font-weight: bold;
    color: #333;
}

.value {
    color: #007bff;
}

.value.email {
    color: #007bff;
}

.ดีมาก {
    color: white;
    background-color: darkgreen;
    font-size: 20px;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 5px;
}

.พอใช้ {
    color: white;
    background-color: yellow;
    font-size: 20px;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 5px;
}

.ปรับปรุง {
    color: white;
    background-color: red;
    font-size: 20px;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 5px;
}

.ดี {
    color: white;
    background-color: green;
    font-size: 20px;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 5px;
}
</style>
<body>


	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="index.php" class="brand">
			<i class='bx bxs-report'></i>
			<span class="text">HR</span>
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
					<i class='bx bxs-user-detail'></i>
					<span class="text">แบบประเมินการทำงาน</span>
				</a>
			</li>
			<li>
				<a href="form_2.php">
					<i class='bx bxs-user-detail'></i>
					<span class="text">แบบประเมินความประพฤติ</span>
				</a>
			</li>
			<li class="active">
				<a href="result.php">
					<i class='bx bxs-user-detail'></i>
					<span class="text">สรุปการประเมิน</span>
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
            <form method="POST" action="result.php">
                <label for="employee">เลือกพนักงาน :</label>
                <select name="employee" id="employee" required>
                    <option value="">เลือก...</option>
                    <?php
                    // Loop through employees and create options
                    if ($employeeResult) {
                        while ($employee = $employeeResult->fetch_assoc()) {
                            // Check if this employee is selected
                            $selected = (isset($_POST['employee']) && $_POST['employee'] == $employee['p_id']) ? 'selected' : '';
                            echo '<option value="' . $employee['p_id'] . '" ' . $selected . '>' . $employee['fname'] . ' ' . $employee['lname'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <button type="submit">Submit</button>
            </form>

            <div class="user-info">
                <div class="user-details">
                    <h2>สรุปการประเมินผลของ <?= isset($personResult['fname']) ? $personResult['fname'] . " " . $personResult['lname'] : ''; ?></h2>
                    <p><span class="label">คะแนนรวม : </span> <?= isset($totalScore) ? $totalScore : ''; ?> / <?= isset($maxScore) ? $maxScore : ''; ?></p>
                    <p><span class="label">เปอร์เซ็นต์ : </span> <?= isset($percentage) ? number_format($percentage, 2) . '%' : ''; ?></p>
                    <p><span class="label">ผลการประเมิน : </span> 
                        <?php if (isset($evaluationResult) && $evaluationResult != ""): ?>
                            <span class="
                                <?php 
                                    if ($evaluationResult == "ดีมาก") echo 'ดีมาก'; 
                                    elseif ($evaluationResult == "ดี") echo 'ดี';
                                    elseif ($evaluationResult == "พอใช้") echo 'พอใช้';
                                    else echo 'ปรับปรุง'; 
                                ?>">
                                <?= $evaluationResult; ?>
                            </span>
                        <?php else: ?>
                            <span class="label"> </span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
                <div class="user-info">
                            <!-- <div class="user-details">
                                <h2>Evaluation Summary for <?= isset($personResult['fname']) ? $personResult['fname'] . " " . $personResult['lname'] : ''; ?></h2>
                                <p><span class="label">Total Score:</span> <?= isset($totalScore) ? $totalScore : ''; ?> / <?= isset($maxScore) ? $maxScore : ''; ?></p>
                                <p><span class="label">Percentage:</span> <?= isset($percentage) ? number_format($percentage, 2) . '%' : ''; ?></p>
                                <p><span class="label">Evaluation Result:</span> <?= isset($evaluationResult) ? $evaluationResult : ''; ?></p>
                            </div> -->

                            <!-- Container for the bar chart -->
                            <canvas id="scoreChart" width="400" height="200"></canvas>
                        </div>
        </div>

        <!-- Add a container for the chart -->
        


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
        document.addEventListener('DOMContentLoaded', function () {
            // Prepare the data for the chart
            const ctx = document.getElementById('scoreChart').getContext('2d');
            const topics = <?= json_encode($topics); ?>;
            const scores = <?= json_encode($scoresData); ?>;
            
            // Create the bar chart
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: topics,
                    datasets: [{
                        label: 'Total Score per Topic',
                        data: scores,
                        backgroundColor: 'rgba(0, 123, 255, 0.7)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 35 // Assuming max score is 35 for each topic (7 questions x 5 points)
                            }
                        }
                    }
                });
            });
        </script>

	</section>
	<!-- CONTENT -->
	
	<script src="script.js"></script>
</body>
</html>