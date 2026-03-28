# Just a personal home page

### It's just a simple package, a script, written in php for personal use. I just want a speeddial like and a internal "site" that I'm going to link to from obs for different scenes, and a tiny homepage for my docker services.

***

### Components(until now, will de updated):
- a tiny router(not for production) with named routes and attribute router and regex to match {:numeric};
```php
 // Add named routes to the router
        $router->addNamedRoute(verb: "GET", route: "/tools", method: "Tools\\Tools::index");
 // Add attribute routes to the router
        $router->addAttributeRoute(routes: ["Stream\\Stream"]);
 // and the atribute on method
    #[Get(route: '/')]
    #[Get(route: '/index')]
    public function index()
    {
        echo "Stream - Index";
    }
```
- cookie with encryption, hmac sign and key rotation;
- custom session handler;
- super tiny basic template engine
- in the work of adding the fs part, extending spl file system classes and adding some methods to them;
- request class/object
- in the work of adding some database layer sqplite/PDO (maybe add some crud capability);
- started to add unit test, maybe take care of coverage;
  ###Components to be added:
- response classes.
- to be added;

### And again this is not intended to be used in production(it's just for in house use)
