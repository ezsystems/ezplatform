Cherry MVC
==========

Cherry: From the fact that we will use components from the sources we see fit:
 - RMF https://github.com/Qafoo/REST-Micro-Framework
 - Symfony https://github.com/symfony/
 - HiMVC https://github.com/andrerom/HiMVC



A prototype of eZ Publish integrations from a high level:


```
>index.php
 |-> boostrap.php |<-> config.php
 |<--- Container ( configured )
 |
 |-> Dispatcher/ PreRouter -> Router -> Controller |<-> Model
 |                                                 | Result -> ViewDispatcher-> View (twig/php based)
 |                                    <--------------------------------  Response 
 |                          <--------
<--------------------------
```


