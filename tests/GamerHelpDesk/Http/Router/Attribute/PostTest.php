<?php

use GamerHelpDesk\Http\Router\Attribute\Post;

class PostTest extends \PHPUnit\Framework\TestCase
{
    public function testPostVerb()
    {
        $post = new Post(route: '/test');
        $this->assertEquals('POST', $post->verb);
    }
}