Cherry MVC
==========

Cherry: From the fact that we will use components from the sources we see fit:
 - RMF
 - Symfony
 - HiMVC



A prototype of eZ Publish integrations from a high level:


```
>index.php
 |-> boostrap.php-> config.php
 |                <- config
 |<- Container ( configured )
 |
 |-> Dispatcher/ PreRouter -> Router -> Controller |<-> Model
 |                                                 | Result -> ViewDispatcher-> View (twig/php based)
<-----------------------------------------------------------------------------  Response 
```


