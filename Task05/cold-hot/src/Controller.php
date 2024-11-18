<?php

namespace Ivante2004\ColdHot\Controller;

use Ivante2004\ColdHot\View\View;
use Ivante2004\ColdHot\Model\Model;

class Controller
{
    public function generateSecretNumber()
    {
        $arrayNumbers = [];
        $length = 3;

        while (count($arrayNumbers) !== $length) {
            $number = rand(1, 9);

            if (!in_array($number, $arrayNumbers)) {
                $arrayNumbers[] = $number;
            }
        }

        $result = implode("", $arrayNumbers);

        return $result;
    }

    public function menu()
    {

        $isExist = file_exists('cold-hot.db');

        View::showGame($isExist);

        $choiceUser = readline("Выберите действие: ");

        switch ($choiceUser) {
            case 1:
                $this->startGame();
                break;
            case 2:
                if ($isExist) {
                    $this->showHistory();
                    break;
                } else {
                    echo "Таблицы не существует";
                    break;
                }
            case 3:
                if ($isExist) {
                    $this->showGameRepeat();
                    break;
                } else {
                    echo "Таблицы не существует";
                    break;
                }
            default:
                echo "Неверный выбор";
                break;
        }
    }

    public function startGame()
    {
        $isFinished = false;
        $counter = 0;
        $hints = [];
        $triesCounter = 0;

        $username = readline("Ваше имя: ");

        if (! file_exists('cold-hot.db')) {
            $model = new Model('cold-hot.db');
            $model->createTables();
        } else {
            $model = new Model('cold-hot.db');
        }

        $secretNumber = $this->generateSecretNumber();
        $gameId = $model->createId();

        while (!$isFinished) {
            $userData = readline("Ваше число (или 'exit' для выхода): ");

            $triesCounter++;

            $counter = 0;
            $hints = [];

            if ($userData === 'exit') {
                $model->storeResult($username, $secretNumber, 0);
                $model->storeTry($gameId, $triesCounter, $userData, 'Проигрыш');

                View::showLose($secretNumber);
                die();
            } elseif (strlen($userData) === strlen($secretNumber)) {
                for ($i = 0; $i < strlen($secretNumber); $i++) {
                    if (mb_strpos($secretNumber, $userData[$i]) !== false) {
                        if (mb_strpos($secretNumber, $userData[$i]) === $i) {
                            $hints[] = 'Горячо';
                        } else {
                            $hints[] = 'Тепло';
                        }
                    } else {
                        $hints[] = 'Холодно';
                    }
                }

                sort($hints);

                foreach ($hints as $status) {
                    if ($status === 'Горячо') {
                        $counter++;
                    }
                }

                if ($counter === 3) {
                    $isFinished = true;

                    $model->storeResult($username, $secretNumber, 1);
                    View::showWin($secretNumber);
                }

                View::showHints(implode(', ', $hints));

                $isFinished ? $model->storeTry($gameId, $triesCounter, $userData, 'Победа') : $model->storeTry($gameId, $triesCounter, $userData, implode(', ', $hints));
            }
        }
    }

    public function showHistory()
    {
        $model = new Model('cold-hot.db');

        $games = $model->getGames();

        View::showHistory($games);
    }

    public function showGameRepeat()
    {
        $gameId = readline("Введите id сохраненной партии: ");

        $model = new Model('cold-hot.db');

        $game = $model->getGame($gameId);

        View::showGameRepeat($game);
    }
}