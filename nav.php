<?php
$user = $_SESSION["user"];
if ($user == "Petugas Pendaftaran") {
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="petugas_pendaftaran.php">Petugas Pendaftaran</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item ">
                    <a class="nav-link" href="petugas_pendaftaran.php">Antrian</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
            </ul>
            <a class="navbar-brand">
                <a class="btn btn-danger ml-3" type="button" href="logout.php">Log Out</a>
        </div>
    </nav>
    <?php
} else if ($user == "Dokter" || $user == "Active") {
    ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <a class="navbar-brand" href="dokter.php">Dokter</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dokter.php">Pemeriksaan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_pasien.php">History Pasien</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                </ul>
                <a class="btn btn-danger ml-3" type="button" href="logout.php">Log Out</a>
            </div>
        </nav>
    <?php
} else if ($user == "Apoteker") {
    ?>

            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <a class="navbar-brand" href="apoteker.php">Apoteker</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="apoteker.php">Antrian</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_pasien.php">History Pasien</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profile</a>
                        </li>
                    </ul>
                    <a class="btn btn-danger ml-3" type="button" href="logout.php">Log Out</a>
                </div>
            </nav>
    <?php

} else if ($user == "Admin") {

    ?>

                <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                    <a class="navbar-brand" href="admin.php">Admin</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <!-- Nav Dropdown -->
                            <li class="nav-item">
                                <a class="nav-link" href="admin.php">Data Pasien</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="data_praktik.php">Data Praktik</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="laporan.php">Laporan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">Profile</a>
                            </li>
                        </ul>
                        <a class="btn btn-danger ml-3" type="button" href="logout.php">Log Out</a>
                    </div>
                </nav>

    <?php

}
?>