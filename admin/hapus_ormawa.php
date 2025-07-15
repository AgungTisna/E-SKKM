<?php
include "../koneksi.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $sql = "DELETE FROM user WHERE id_user='$id'";
    $conn->query($sql);

    $sql_ormawa = "DELETE FROM user_detail_ormawa WHERE id_user='$id'";
    $conn->query($sql_ormawa);

    header("Location: index.php");
}
?>
