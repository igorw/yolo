<?php
/**
 * Created by PhpStorm.
 * User: praca
 * Date: 25.03.16
 * Time: 09:51
 */

namespace yolo;


class selfie
{
    /**
     * @var array
     */
    private $friends = [];
    /**
     * return your selfie
     * @return $this
     */
    function __invoke()
    {
        return array_merge($this->friends, [new \ReflectionClass($this)]);
    }

    /**
     * makes you a good selfie mood
     */
    function mood()
    {
        header('Location: https://www.youtube.com/watch?v=kdemFfbS5H0');
        exit;
    }

    /**
     * add new friends to your selfie
     * @param mixed $friends
     */
    function friends($friends)
    {
            $this->add_friend((object)$friends);
    }

    private function add_friend($friend)
    {
        $this->friends[] = new \ReflectionClass($friend);
    }


}
