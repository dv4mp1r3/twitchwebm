<?php 
namespace twitchwebm\controllers;

use twitchwebm\models;
use mmvc\core\AccessChecker;
use mmvc\controllers\WebController;

class VideoController extends WebController
{

    public function __construct()
    {
        $this->rules = [
            'upload' => [
                self::RULE_TYPE_ACCESS_GRANTED => AccessChecker::USER_ALL,
                self::RULE_TYPE_INPUT => self::INPUT_PARAMETER_REQUEST,
            ],
            'remove' => [
                self::RULE_TYPE_ACCESS_GRANTED => AccessChecker::USER_ALL,
                self::RULE_TYPE_INPUT => self::INPUT_PARAMETER_REQUEST,
            ],
        ];
        parent::__construct();
    }

    public function actionUpload()
    {
        header('Content-type: application/json; charset=utf-8');
        try {
            $name = $_POST['user_name'];
            $user = models\User::select('id')->where('name = :name', ['name' => $name])->execute();

            if (count($user) == 0) {
                $user = new models\User();
                $user->name = $name;
                $user->save();
            } else
                $user = $user[0];

            $video = new models\Video();
            $video->url = $_POST['video_url'];
            $video->user_id = $user->id;
            $video->save();
            
            $params = ['isAdmin' => isset($_SESSION['auth']) && $_SESSION['auth'] === true,
                'video' => [
                    'url' => htmlspecialchars($video->url),
                    'id' => intval($video->id),
                    'username' => htmlspecialchars($user->name),
                ],
            ];

            $result = [
                'html' => $this->getHtmlContent('views/home/webm_block.php', $params),
                'url' => htmlspecialchars($video->url),
            ];

            $json_result = json_encode(
                [
                    'error' => 0,
                    'data' => $result,
                ]
            );

            return $json_result;
        } catch (\Exception $ex) {
            return json_encode(['error' => 1,
                'data' => $ex->getMessage()]);
        }
    }

    public function actionRemove()
    {
        header('Content-type: application/json; charset=utf-8');
        if (isset($_POST['video_id']))
            return $this->actionHide($_POST['video_id']);

        return json_encode(['error' => 1, 'data' => 'Wrong API parameters']);
    }

    public function actionUpdate()
    {
        header('Content-type: application/json; charset=utf-8');
        if (isset($_POST['video_id']))
            return $this->actionHide($_POST['video_id'], true);

        return json_encode(['error' => 1, 'data' => 'Wrong API parameters']);
    }

    private function actionHide($id, $current = false)
    {
        header('Content-type: application/json; charset=utf-8');
        if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
            return json_encode(['error' => 1, 'data' => 'Access level error']);
        }

        try {
            $result = models\Video::update(['is_viewed' => 1])->where('id =' . intval($id))->execute();
            $_SESSION['obs'] = ['id' => $id, 'current' => $current, 'error' => intval(!$result)];
            return json_encode(['error' => intval(!$result)]);
        } catch (\Exception $ex) {
            return json_encode(['error' => 1, 'data' => $ex->getMessage()]);
        }
    }

    public function actionGetnew()
    {
        header('Content-type: application/json; charset=utf-8');
        if (!isset($_POST['old_ids'])) {
            return json_encode(['error' => 1, 'data' => '']);
        }

        $in_array = $_POST['old_ids'];
        $notInStr = !empty($in_array) ? "video.id not in ($in_array)" : '1';
        $videos = models\Video::select(['video.id video_id', 'video.url url', 'user.name username'])->
                join(models\Video::JOIN_TYPE_LEFT, 'user', 'user.id = video.user_id')->
                where("$notInStr and video.is_viewed=0")->execute();

        $v_res = ['error' => 0, 'data' => []];
        if (!is_bool($videos)) {
            foreach ($videos as $video) {
                $params = ['isAdmin' => isset($_SESSION['auth']) && $_SESSION['auth'] === true,
                    'video' => [
                        'url' => htmlspecialchars($video->url),
                        'id' => intval($video->video_id),
                        'username' => htmlspecialchars($video->username),
                    ],
                ];
                array_push($v_res['data'], [
                    'html' => $this->getHtmlContent('views/home/webm_block.php', $params),
                    'url' => htmlspecialchars($video->url),
                ]);
            }
        }

        return json_encode($v_res);
    }

    public function actionObs()
    {
        header('Content-type: application/json; charset=utf-8');
        if (isset($_REQUEST['obs']) && boolval($_REQUEST['obs']) === true) {
            $json_result = json_encode($_SESSION['obs']);
            unset($_SESSION['obs']);
            $_SESSION['obs']['error'] = 0;
            return $json_result;
        }

        return json_encode(['error' => 1, 'data' => 'Access level error']);
    }
}
