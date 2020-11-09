<?php
// lisätään config 
require_once "config.php";
 
// määritellään muuttujat
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // validoidaan käyttäjänimi
    if(empty(trim($_POST["username"]))){
        $username_err = "Syötä käyttäjätunnus.";
    } else{
        
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
       
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // parametrit
            $param_username = trim($_POST["username"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Tämä käyttäjätunnus on jo käytössä.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Hups! Jotain meni vikaan. Kokeile myöhemmin uudestaan.";
            }

            // sulje statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validoi salasana
    if(empty(trim($_POST["password"]))){
        $password_err = "Syötä salasana.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Salasanan täytyy sisältää ainakin 6 merkkiä.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validoi toistettu salasana
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Toista salasana.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Salasanat eivät täsmää.";
        }
    }
    
    // tarkistetaan, että errorit tyhjät
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // parametrit
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // HASHATAAN SALASANA
            
            if(mysqli_stmt_execute($stmt)){
                
                // ohjataan kirjaudu sivulle
                header("location: kirjaudu.php");
            } else{
                echo "Jotain meni vikaan. Kokeile myöhemmin uudestaan.";
            }

            // sulje statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // sulje tietokantayhteys
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Luo tili</title>
    
    
</head>

<?php include('includes/head.php') ?>

<body>
<div class="container" id="containerKirjautuminen">
  <div class="divKirjautuminen1">
    
        <h2>Luo uusi tili</h2>
        <p>Täytä alla olevat tiedot luodaksesi uuden tilin.</p>
        <div class="divKirjautuminen2">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Sähköpostiosoite</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Salasana</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Toista salasana</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1" required>
                <label class="form-check-label" for="exampleCheck1">Hyväksyn <a href="RTP_Kayttajaehdot.php">käyttäjäehdot</a></label>
            </div>
            <br>
            <div class="form-group">
                <input type="submit" class="btn btn-info" value="Luo tili">
                <input type="reset" class="btn btn-default" value="Tyhjennä">
            </div>
            </div>
            <p class="pTerapeutti">Onko sinulla jo tili? <a href="kirjaudu.php">Kirjaudu sisään</a></p>
            <p>Tai palaa takaisin <a href="index.php">etusivulle</a></p>
        </form>
        
     
  </div>
  
</div>  
    
<?php include('includes/footer.php') ?>
</body>
</html>