<?php
	include 'Genericas/logado.php';
	include 'Genericas/conecta.php';
?>

<?php
	if (isset($_POST['subdivisao'])) {
		$_SESSION['subdivisao'] = $_POST['subdivisao'];
	}
?>
<script>
	
let hour = 0;
let minute = 0;
let second = 0;
let millisecond = 0;

let cron;

document.getElementById('iniciar').onclick = () => start();
document.getElementById('pausar').onclick = () => pause();
document.getElementById('resetar').onclick = () => reset();

function start() {
  pause();
  cron = setInterval(() => { timer(); }, 10);
}

function pause() {
  clearInterval(cron);
}

function reset() {
  minute = 0;
  second = 0;
  millisecond = 0;

  document.getElementById('minute').innerText = '00';
  document.getElementById('second').innerText = '00';
}

function timer() {
  if ((millisecond += 10) == 1000) {
    millisecond = 0;
    second++;
  }
  if (second == 60) {
    second = 0;
    minute++;
  }
  document.getElementById('minute').innerText = returnData(minute);
  document.getElementById('second').innerText = returnData(second);
}

function returnData(input) {
  return input > 10 ? input : `0${input}`
}
</script>
<!DOCTYPE html>
<html>
	<head>
		<link rel="icon" type="image/png" sizes="32x32" href="favicon/logo.png">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="Css.css">
		<link href="https://fonts.googleapis.com/css?family=Chakra+Petch" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
		<title>Sorteio</title>
		<style type="text/css">
			.loader {
				position: relative;
				width: 150px;
				margin-left: auto;
				margin-right: auto;
				margin-top: 20px;
				margin-bottom: 20px;
				border: 16px solid #e3e3e3;
				border-radius: 50%;
				border-top: 16px solid #c9c9c9;
				width: 120px;
				height: 120px;
				-webkit-animation: spin 2s linear infinite;
				animation: spin 2s linear infinite;
			}
			@keyframes spin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
			}
		</style>
		<?php include 'Genericas/estilo.php'; ?>
	</head>
	<body class="bodyLaudo" style="background-color: <?php echo $_SESSION['corfundo']; ?>;">
		<div id="particles-js" ></div>
			<?php include 'Genericas/insereParticulas.php';?>
			<div style="text-align: center;">
				<img src="<?php echo $_SESSION['logo']; ?>"	class="headerImg" alt="Logo" onclick="volta()" style="cursor: pointer;">
			</div>
			<div class="page-wrapper font-poppins">
				<div class="wrapper wrapper--w680">
					<div class="card card-4">
						<div class="card-body">
							<h2 class="title">Sorteio</h2>
							<form method="POST" id="myform">
								<input type="submit" name="Sortear" id="Sortear" class="btn btn--radius-2" style="background-color: <?php echo $_SESSION['corfundo']?>;" value="Sortear!"/>
								<div style="text-align: center;">
									<div class="loader" id="loader" style="display: none;"></div>
									<div style="font-size: 45px;">
										<span id="minute"  >00</span>:<span id="second"  >00</span>
									</div>
									<div>
										<b><span id="iniciar" style="color:<?php echo $_SESSION['corfundo']?>;">Iniciar</span>     <span id="pausar" style="color:<?php echo $_SESSION['corfundo']?>;">Pausar</span>     <span id="resetar" style="color:<?php echo $_SESSION['corfundo']?>;">Reiniciar</span></b>
									</div>
									<button class="btn btn--radius-2" type="button" onclick="start" name="start" style="line-height: all; color:<?php echo $_SESSION['corfundo']?>; border-style:solid; border-width: thin; background-color: white; border-color: <?php echo $_SESSION['corfundo']?>;">Iniciar</button>
									<button class="btn btn--radius-2" type="button" onclick="pause" name="pause" style="line-height: all; background-color: white; border-color: <?php echo $_SESSION['corfundo']?>;">Pausar</button>
									<button class="btn btn--radius-2" type="button" onclick="reset" name="reset" style="line-height: all; background-color: white; border-color: <?php echo $_SESSION['corfundo']?>;">Reiniciar</button>
									<?php
										if (isset($_POST["Sortear"])) {
											$sql="SELECT * FROM saphira_presenca WHERE ID_subdivisoes='".$_SESSION['subdivisao']."' ORDER BY RAND() LIMIT 1"; //Usando o operador newID() sortear um vencedor da lista de presentes na palestra
											$result = mysqli_query($link, $sql);
											if (mysqli_num_rows($result) >= 1) {
												$row = mysqli_fetch_assoc($result);
												$sql = "SELECT * FROM saphira_pessoa WHERE ID_pessoa='".$row['ID_pessoa']."'";
												$result = mysqli_query($link, $sql);
												if (mysqli_num_rows($result) >= 1) {
													$row = mysqli_fetch_assoc($result)
													?><h2 style="font-size: 5em; display: none; margin-bottom: 0;" class="title" id="sorteado"><?php echo $row['Nome'];?></h2><?php 
												}
												?> 
												<script type="text/javascript">
													document.getElementById('loader').style.display = "block";
													setTimeout(
														function() {
															document.getElementById('loader').style.display = "none";
															document.getElementById('sorteado').style.display = "block";
															start()
														},
														(Math.random() * 1000) + 500
													);
												</script> <?php
											}
										}
									?>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
	<?php include 'Genericas/voltar.php' ?>
</html>
<script type="text/javascript">
function envia() {
	document.getElementById('Sortear').value = "aaaa";
	document.getElementById('myform').submit();
}
</script>
