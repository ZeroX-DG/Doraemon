<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
    <link rel="stylesheet" href="<?php echo assets('assets/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo assets('assets/bootstrap/css/style.css'); ?>">
</head>

<body>
    <nav class="navbar navbar-inverse navbar-static-top">
        <div class="container-fluid">
            <div class="navbar-header"><a class="navbar-brand navbar-link" href="#">Doraemon </a>
                <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
            </div>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav">
                    <li class="active" role="presentation"><a href="#">Tính lương</a></li>
                    <li role="presentation"><a href="#">Quản lý lịch làm việc</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        {{ $hello }}
        <h1 class="text-center">Quản lý Doraemon</h1>
        <div class="row">
            <div class="col-md-6 col-md-offset-3"><a class="btn btn-primary btn-block btn-lg" role="button" href="#">Tính lương nhân viên</a>
                <a class="btn btn-info btn-block btn-lg" role="button" href="#">Quản lý lịch làm việc</a>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>