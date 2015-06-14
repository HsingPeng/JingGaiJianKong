<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-cn">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <title>index</title>

    <link rel="stylesheet" href="/JianKong/Public/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/JianKong/Public/Home/css/index.css" class="stylesheet">

    <script type="text/javascript" src="/JianKong/Public/bootstrap/js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="/JianKong/Public/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/JianKong/Public/bootstrap/js/holder.min.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mCdkQAiYb4a7X2tm78Z2uo3n"></script>

</head>

<body>
    <div class="container">
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">

            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/JianKong/home.php/Home/Index">井盖监控系统</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-left">
                        <li class="active"><a href="#">主页</a>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right" >
                        <li><a href="#">登出</a>
                        </li>
                    </ul>
                </div>

        </nav>
        </div>
        
        <nav class="navbar navbar-default navbar-fixed-bottom">
  <div class="container-fluid">
   <button type="button" class="btn btn-default navbar-btn suojing"><span class="glyphicon glyphicon-chevron-left"></span></button>
  </div>
</nav>
        
    
        <div class="table-responsive float-left">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>设备ID</th>
                        <th>地址</th>
                        <th>状态</th>
                        <th>角度/°</th>
                        <th>电池电压/v</th>
                        <th>通信时间</th>
                    </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
        </div>
        
        <div class="map">
           
            <div id="allmap">   
            </div>
     </div>
     
      
       
        
</body>

<script type="text/javascript" src="/JianKong/Public/Home/js/index.js"></script>

<script>
    jQuery(document).ready(init());
</script>

</html>