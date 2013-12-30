<head>
    <link rel="shortcut icon" href="/favicon.ico" type="image/icon">
<!--    <link href="--><?//=base_url("js/jquery-ui.css");?><!--" rel="stylesheet" type="text/css"/>-->
    <script src="<?=base_url("js/jquery-1.9.0.min.js");?>"></script>
    <script src="<?=base_url("js/jquery-ui-1.9.2.custom.min.js");?>"></script>
<!--    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>-->
<!--    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>-->

    <!--    <script src="--><?//=base_url("js/form.js");?><!--"></script>-->
    <script src="<?=base_url("js/sync.js");?>"></script>
    <style type="text/css">
        #draggable {
            border: 0px solid red;
            z-index: 100;
            position: absolute;
            width: 50px;
            height: 50px;
            background: silver;
        }

        #city {
            background-color: red;
            border: 0px solid red;
            position: absolute;
            top: 300px;
            width: 100px;
            height: 100px;
        }

        #city div {
            background: #ccf;
            height: 100px;
        }

        #users {
            float: left;
        }
        #chats li{
            float:left;
            clear:both;
            background:#1af;
            list-style: none;
            border-radius: 10px;
            padding:0 10px;
            border: 2px #ccc solid;
            color:#333;
            background:rgb(101,200,85);
        }
        #chats li span{
            color:#333;
        }
        .unit {border:3px solid blue;}
    </style>
    <?php Battle::getHeader($gameName, $playerData, $arg);?>
</head>