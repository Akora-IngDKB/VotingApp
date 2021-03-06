<?php
require('connection.php');

session_start();
if (empty($_SESSION['fname'])) {
    header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('includes/header.php'); ?>
    <?php include_once 'includes/bootstrapCss.php' ?>
</head>

<body>
    <?php include_once('includes/navbar.php'); ?>
    <div class="row center" style="margin-top: 50px; margin-bottom: 50px">
        <div style="margin-left: 45px">
            <h1 class="font-weight-bold" style="margin-bottom: 0"><img src="images/candidate-2.gif" height='30px' alt="" />ELECTION <?php echo Date('Y') ?></h1>
            <h4 style="margin-left: 10px">PARLIAMENTARY BALLOT</h4>
        </div>
        <form method="POST">
            <table style="margin-top: 30px" class="table-striped">
                <thead>
                    <tr class="font-weight-bold">
                        <th scope="col">Candidate</th>
                        <th scope="col">Party</th>
                        <th scope="col"> Vote</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require('connection.php');
                    $get_constituency = "SELECT const_id FROM constituency WHERE constituency = '$_SESSION[constituency]'";
                    $run_result = mysqli_query($con, $get_constituency);
                    $const_id = mysqli_fetch_array($run_result);

                    $sql = "SELECT * from par_candidates WHERE  const_id = '$const_id[const_id]'";
                    $result = mysqli_query($con, $sql);

                    while ($row = mysqli_fetch_array($result)) {
                        $name = $row['candidate_name'];
                        $img = $row['candidate_img'];
                        $party = $row['party_id'];
                        $can_id = $row['candidate_id'];

                        $run_id = "SELECT * FROM party WHERE party_id = '$party'";
                        $run = mysqli_query($con, $run_id);
                        $array = mysqli_fetch_array($run);

                        echo "<tr>
                               <td name='candidate_name'> $img</td>
                               <td class='font-weight-bold'> " . $array['party_name'] . "&nbsp;&nbsp; <img style='width: 50px; height: 50px' src='images/" . $array['party_logo'] . "' /></td>
                               <td><input type='submit' name='vote' value='$name' class='btn'/></td>
                             </tr> ";
                    }
                    ?>

                </tbody>
            </table>
        </form>
        <a href="selectPosition.php" class="text-dark mt-3">
            <h5 class="font-weight-bold "> <i class="fas fa-angle-double-left"></i>Go Back </h5>
        </a>
    </div>
    <?php include_once 'includes/footer.php' ?>

    <?php include_once 'includes/bootstrapJs.php' ?>
</body>

</html>


<?php
require('connection.php');

if (isset($_POST['vote'])) {
    $voted = "SELECT par_voted FROM `voters` WHERE member_id='$_SESSION[member_id]'";
    $run_voted = mysqli_query($con, $voted);
    $row_voted = mysqli_fetch_array($run_voted);

    if ($row_voted['par_voted'] == true) {
        echo "<script>alert('You have already voted')</script>";
    } else {
        $sql = "UPDATE `par_candidates` SET `candidate_votes` = candidate_votes + 1   WHERE candidate_name= '$_POST[vote]'";
        $run = mysqli_query($con, $sql);

        if ($run) {
            $update = "UPDATE `voters` SET par_voted = true WHERE member_id = '$_SESSION[member_id]'";
            $run_update = mysqli_query($con, $update);

            if ($run_update) {
                echo "<script>alert('You casted your vote for $_POST[vote]')</script>";
                echo  "<script>window.open('selectPosition.php', '_self')</script>";
            } else {
                echo "<script>alert('Update was unsuccessful')</script>";
            }
        } else {
            echo "<script>alert('Your vote didn't go through...Please vote again')</script>";
        }
    }
}

?>