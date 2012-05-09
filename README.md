Cherry MVC
==========

Cherry: From the fact that we will use components from the sources we see fit:
 - RMF https://github.com/Qafoo/REST-Micro-Framework
 - Symfony https://github.com/symfony/
 - HiMVC https://github.com/andrerom/HiMVC



A prototype of eZ Publish MVC stack from a high level:


```
>index.php
 |-> boostrap.php |<-> config.php
 |<--- Container ( configured )
 |
 |-> RequestParser
 |<---- Request
 |
 |-> Dispatcher/ PreRouter -> Router -> Controller |<-> Model
 |                                                 | Result -> ViewDispatcher-> View (twig/php based)
 |                                    <--------------------------------  Response 
 |                          <--------
<--------------------------
```


For the Legacy integration there are two possible ways to inject logic:
- Allow pre router to take several routers (new stack and legacy fallback)
- Have a Special Legacy Route that serves as a fallback if no other route matches

Last option is the one recently prototyped in HiMVC:
https://github.com/andrerom/HiMVC/compare/2f12b65...c4ecefe
Reason is that it is then optional, you can disable it by removing the setting for it, and what remain is a clean HMVC architecture with out any hard coded Legacy knowledge.
In the case of HiMVC, the above high level view is how it is implemented with the execption of Response object.
And as for Reouter, the details is that it finds a match (Route object), and executes controller callback (so either closure or other callable structure), which in "LegacyKernel" means it will call Legacy\Kernel::run().

