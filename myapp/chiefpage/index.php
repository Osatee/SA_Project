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
	if ($stmt === false) {
		die("Error in query: " . $conn->errorInfo()[2]); // แสดงข้อผิดพลาด
	}
	$data = $stmt->fetch(PDO::FETCH_ASSOC);
	$person = $data['persons']; // เก็บค่าที่ query ได้ไว้ในตัวแปร

	// Query ข้อมูลจากฐานข้อมูล
	$sql = "SELECT COUNT(*) AS total_evaluators FROM (
														SELECT DISTINCT evaluator
														FROM form
														) AS subquery;";
	$stmt = $conn->query($sql);
	if ($stmt === false) {
		die("Error in query: " . $conn->errorInfo()[2]);
	}
	$totalEvaluators = $stmt->fetch(PDO::FETCH_ASSOC)['total_evaluators'];

	$sql = "SELECT COUNT(p.p_id) AS count_not_in_evaluator FROM person p LEFT JOIN form f ON p.p_id = f.evaluator WHERE f.evaluator IS NULL;";
	$stmt = $conn->query($sql);
	if ($stmt === false) {
		die("Error in query: " . $conn->errorInfo()[2]);
	}
	$result = $stmt->fetch(PDO::FETCH_ASSOC)['count_not_in_evaluator'];

	$sql = "SELECT COUNT(*) AS forms FROM form"; // นับจำนวนรายการในตาราง person
	$stmt = $conn->query($sql);
	if ($stmt === false) {
		die("Error in query: " . $conn->errorInfo()[2]);
	}
	$data = $stmt->fetch(PDO::FETCH_ASSOC);
	$forms = $data['forms'];

	$sql = "SELECT p_id as personId FROM person";
	$stmt = $conn->query($sql);
	if ($stmt === false) {
		die("Error in query: " . $conn->errorInfo()[2]);
	}
	$data = $stmt->fetch(PDO::FETCH_ASSOC);
	$personId = $data['personId'];

	$sql = "SELECT topic_id as topicId FROM topic";
	$stmt = $conn->query($sql);
	if ($stmt === false) {
		die("Error in query: " . $conn->errorInfo()[2]);
	}
	$data = $stmt->fetch(PDO::FETCH_ASSOC);
	$topicId = $data['topicId'];

	// ฟังก์ชันสำหรับดึงข้อมูลกราฟ
function getGraphData($conn, $selectedPerson, $selectedTopic) {
    $sql = "SELECT 
                AVG(score1) AS avg_score1,
                AVG(score2) AS avg_score2,
                AVG(score3) AS avg_score3,
                AVG(score4) AS avg_score4,
                AVG(score5) AS avg_score5,
                AVG(score6) AS avg_score6,
                AVG(score7) AS avg_score7
            FROM form
            WHERE evaluatee = :p_id AND topic_id = :topic_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':p_id', $selectedPerson, PDO::PARAM_INT);
    $stmt->bindParam(':topic_id', $selectedTopic, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	// ดึงรายชื่อพนักงานทั้งหมด
	$sql = "SELECT p_id, fname FROM person";
	$stmt = $conn->query($sql);
	$persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// ดึงรายการ topic ทั้งหมด
	$sql = "SELECT topic_id, topic_name FROM topic";
	$stmt = $conn->query($sql);
	$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// ตรวจสอบการเลือก person และ topic
	$selectedPerson = isset($_GET['person']) ? $_GET['person'] : $persons[0]['p_id'];
	$selectedTopic = isset($_GET['topic']) ? $_GET['topic'] : $topics[0]['topic_id'];

	// ดึงข้อมูลกราฟ
	$graphData = getGraphData($conn, $selectedPerson, $selectedTopic);

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
        .dashboard-graph {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        .dashboard-graph select {
            padding: 12px 20px;
            font-size: 16px;
            border: 2px solid #3498db;
            border-radius: 8px;
            background-color: #fff;
            color: #3498db;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-bottom: 20px;
        }
        .dashboard-graph select:hover {
            background-color: #3498db;
            color: #fff;
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
				<a href="feedback.php">
					<i class='bx bxs-comment-detail'></i>
					<span class="text">ข้อเสนอแนะ</span>
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

			<ul class="box-info">
				<li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<p>พนักงานทั้งหมด</p>
						<h3><?php echo$person ?></h3>
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

            <div class="dashboard-graph">
                <form action="" method="get">
                    <select name="topic" onchange="this.form.submit()">
                        <?php foreach ($topics as $topic): ?>
                            <option value="<?php echo $topic['topic_id']; ?>" <?php echo $selectedTopic == $topic['topic_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($topic['topic_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
					<select name="person" onchange="this.form.submit()">
                        <?php foreach ($persons as $person): ?>
                            <option value="<?php echo $person['p_id']; ?>" <?php echo $selectedPerson == $person['p_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($person['fname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <canvas id="scoreChart"></canvas>
            </div>
		</main>

		<script>
			var selectedTopic = <?php echo json_encode($selectedTopic); ?>;
			
			var backgroundColor, borderColor;

			if (selectedTopic == 1) {
				backgroundColor = 'rgba(52, 152, 219, 1)'; // สีฟ้า
				borderColor = 'rgba(52, 152, 219, 1)';
			} else if (selectedTopic == 2) {
				backgroundColor = 'rgba(255, 99, 132, 1)'; // สีชมพู
				borderColor = 'rgba(255, 99, 132, 1)';
			}

			var ctx = document.getElementById('scoreChart').getContext('2d');
			var scoreChart = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['Score 1', 'Score 2', 'Score 3', 'Score 4', 'Score 5', 'Score 6', 'Score 7'],
					datasets: [{
						label: 'Average Scores',
						data: [
							<?php echo $graphData['avg_score1']; ?>,
							<?php echo $graphData['avg_score2']; ?>,
							<?php echo $graphData['avg_score3']; ?>,
							<?php echo $graphData['avg_score4']; ?>,
							<?php echo $graphData['avg_score5']; ?>,
							<?php echo $graphData['avg_score6']; ?>,
							<?php echo $graphData['avg_score7']; ?>
						],
						backgroundColor: backgroundColor,
						borderColor: borderColor,
						borderWidth: 1
					}]
				},
				options: {
					scales: {
						y: {
							beginAtZero: true,
							max: 5
						}
					},
					responsive: true,
					plugins: {
						legend: {
							position: 'top',
						},
						title: {
							display: true,
							text: 'Average Scores by Category'
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