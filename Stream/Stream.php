<?php
/**
 * File: Stream.php
 * Project: GamerHelpDesk
 * Created Date: March 2026
 * Author: danutz0501 (M. Dumitru Daniel)
 * -----
 * Last Modified:
 * Modified By:
 * -----
 * Copyright (c) 2026 M. Dumitru Daniel (M. Dumitru Daniel)
 *  This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Stream;

use GamerHelpDesk\Http\Router\{
    Router,
    Attribute\Get,
    Attribute\Post

};
use GamerHelpDesk\Exception\{
    GamerHelpDeskException,
    GamerHelpDeskExceptionEnum
};
use GamerHelpDesk\View\View;

class Stream
{
    public function __construct(public string $viewPath = VIEWS_PATH . "views" . DIRECTORY_SEPARATOR . "stream" . DIRECTORY_SEPARATOR,
                                public string $customCss = '')
    {
        
    }
    #[Get(route: '/')]
    #[Get(route: '/index')]
    public function index()
    {
        $view = new View($this->viewPath . 'stream');
        $view->assign('title', 'Stream - Index');
        $view->assign('customCss', $this->customCss);
        echo $view->render();
    }

    #[Get(route: '/start')]   
     public function start()
    {
        $view = new View($this->viewPath . 'stream-starting');
        $view->assign('title', 'Stream - Starting');
        $view->assign('customCss', $this->customCss);
        echo $view->render();
    }

    #[Get(route: '/stop')]
    public function stop()
    {
        echo "Stopping Stream...<br>";
    }

    #[Get(route: '/brb')]
    public function brb()
    {
        echo "Be right back - Stream...<br>";
    }

    #[Get(route: '/show-image/{#imageNumber :number}')]
    public function showImage(...$images)
    {
        echo "Stream - Show Image number:  " . $images["imageNumber"] . "<br>";
    }
    
}