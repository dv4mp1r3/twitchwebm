<?php

?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Добро пожаловать, снова">
        <meta name="author" content="dv4mp1r3">
        <title><?= $isOBS == false ? 'WebMDJ' : 'OBS' ?></title>
        <link href="<?=$www_root?>/assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?=$www_root?>/assets/css/1-col-portfolio.css" rel="stylesheet">
        <?php if ($isOBS): ?>
            <link href="<?=$www_root?>/assets/css/obs.css" rel="stylesheet">
        <?php endif; ?>
    </head>
    <body>
        <div class="container">
            <div class="hidden-for-obs row form-row">
                <form id="frm-upload" action="index.php?u=video-upload" method="post">
                    <div class="row form-row">
                        <input class="input-text" name="video.url" placeholder="Ссылка на видео">
                        <span class="glyphicon" id="ico-status"><span>
                    </div>
                    <div class="row form-row">
                        <input class="input-text" name="user.name" placeholder="Кто добавил">
                        <input type="submit" value="Добавить">
                    </div>                    
                </form>
            </div>
            <?php if ($isAdmin && count($videos)): ?>
                <div class="hidden-for-obs row current-button">
                    <a id="btn_skip" class="btn btn-primary" target="_blank" href="#">Пропустить текущее</a>
                </div>
            <?php endif; ?>            
            <div class="row">
                <video id="webm_player" src="<?=$videos[0]['url'] ?>" type="video/webm" <?= $isOBS == false ? 'controls' : 'controls autoplay' ?>>
                </video>
            </div>
            <div class="hidden-for-obs row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        <?php if (count($videos)): ?>
                            Список загруженных видео
                        <?php else: ?>
                            Список пуст
                        <?php endif; ?>
                    </h1>
                </div>
            </div>
            <div id="playlist" class="hidden-for-obs row">
                <?php foreach ($videos as $video): ?> 
                    <div video_id="<?=$video['id']?>" orig_url="<?=$video['url']?>" class="hidden-for-obs row row_video">
                        <div class="col-md-7">
                            <?php if ($isAdmin): ?>
                                <a video_id="<?=$video['id']?>" orig_url="<?=$video['url']?>" class="btn btn-primary btn-remove-video" onclick="remove_click(<?=$video['id']?>)">
                                Удалить из списка
                                </a>
                            <?php endif; ?>
                            <p>Добавил: <?=$video['username']?></p>
                            <p>Ссылка: <a href="<?=$video['url']?>" target="_blank"><?=$video['url']?></a></p>
                            <!--<canvas id="canvas-<?=$video['id']?>">
                            </canvas>-->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <footer class="hidden-for-obs">
                <div class="row">
                    <div class="col-lg-12">
                        <p>Copyright &copy; <?= date('Y'); ?></p>
                    </div>
                </div>
            </footer>
        </div>        
        <script src="<?=$www_root?>/assets/js/jquery.js"></script>
        <script src="<?=$www_root?>/assets/js/bootstrap.min.js"></script>
        <script id="playlist_settings">
            var videoPlayer = document.getElementById('webm_player'); 
            videoPlayer.volume = 0.0;
        </script>
        <script src="<?=$www_root?>/assets/js/user_logic.js"></script>
        <?php if ($isAdmin): ?>
            <script src="<?=$www_root?>/assets/js/admin_logic.js"></script>
        <?php endif; ?>
        <?php if ($isOBS): ?>
            <script src="<?=$www_root?>/assets/js/obs_logic.js"></script>
        <?php endif; ?>
        <script>              
            videoPlayer.onended = function ()
            {
                nextVideo(<?= $isAdmin ? 1 : 0 ?>);
            }
        </script>
    </body>

</html>