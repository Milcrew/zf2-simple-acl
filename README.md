ZF2 Simple Acl
===========
Simple acl module. 

Restrict ROUTES
----------------
Only one thing you need to start is to define routes which you want to restrict and the strategy which you would like
to use for other not defined routes. Strategy might be '<b>permissive</b>' or '<b>strict</b>'. If value '<b>permissive</b>' it will mean
"allow all from all" so then you will have to restrict all resources which you want to be restricted.
If value '<b>strict</b>' it is will mean "deny all from all" so then you will have to allow every resource which you want to be available
```
'restriction_strategy' => 'permissive',
        
'routes' => array(
  'main' => array(true)
)
```

Smart redirection
-----------------
Provide smart redirection according on path/module where user requested restricted resource. 
It is can be used for restricting few modules with on ACL library.
```
'redirect_route' => array('/frontend.*?/'=>'frontend/user/login',
                          '/backend.*?/'=>'backend/user/login')
```
It is very usefull if you have authorization on your frontend and backend modules, 
and you would like to redirect user who requested backend resource to the backend/user/login route and 
users who requested frontend restricted resource to the frontend/user/login.

Recognizers
----------------
It is very simple ability to authorize user by some token. Now supports authorization only thru token defined in COOKIE. 
I've added this feature only because from time to time i need user role which can acces 
to the special resources but without real authorization thru login form. In my example it was a cron script.
```
wget http://$1/generate/sitemap --no-cookies --header "Cookie: cron=a23b4cdg76fb38a5d48b83e22f0e79df" -o /dev/null -O /tmp/sitemap.generated
```
you should specify recognizer:
```
'recognizers' => array('cron'),
```
and role :
```
  'roles' => array(array('name'=>'cron',
                         'id'=>2,
                         'data' => array(
                             'type'=>'cron',
                             // It is authorization token, value will be compared with
                             // token inside $_COOKIE['cron'] and will decide that this is
                             // cron role.
                             'token'=>'a23b4cdg76fb38a5d48b83e22f0e79df'
  )))
```
