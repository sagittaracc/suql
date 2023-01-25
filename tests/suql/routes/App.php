<?php

namespace test\suql\routes;

use suql\syntax\entity\Route;
use suql\syntax\entity\SuQLRoute;

class App extends SuQLRoute
{
    #[Route('/site/main/(\d+)/(\w+)')]
    public function someAction($id, $name)
    {
        return "I am working with an element by id $id and its name is $name";
    }
}
